<?php

class Controller_Management_Bulletin extends Controller_Management_Application
{    
    const PAGER_ITEMS_PER_PAGE = 20;
    const PAGER_WINDOW_SIZE    = 10;

    public function __construct()
    {
        $this->imageDir = Uploader_File::UPLOAD_DIR_NAME . '/bulletin';
    }

    public function setUp()
    {
        parent::setUp();

        if (empty($this->loginManager)) {
            $this->redirect('management/login.php');
        }
    }

    public function index()
    {
        $conditions = [];
        $whereArgs  = [];

        $searchConditions = $this->getSearchConditions();

        $title     = $searchConditions['title'];
        $body      = $searchConditions['body'];
        $hasImage  = $searchConditions['has_image'];
        $isDeleted = $searchConditions['is_deleted'];

        $likeSearchConditions = [
            'title' => $title,
            'body'  => $body,
        ];

        foreach ($likeSearchConditions as $key => $value) {
            if (isset($likeSearchConditions[$key])) {
                $conditions[]    = "{$key} LIKE :{$key}";
                $whereArgs[$key] = "%{$value}%";
            }
        }

        if (!is_empty($isDeleted)) {
            $conditions[] = 'is_deleted = :is_deleted';

            if ($isDeleted === '1') {
                $whereArgs['is_deleted'] = '1';
            } elseif ($isDeleted === '0') {
                $whereArgs['is_deleted'] = '0';
            }
        }

        if (!is_empty($hasImage)) {
            if ($hasImage === '1') {
                $conditions[] = 'image IS NOT NULL';
            } elseif ($hasImage === '0') {
                $conditions[] = 'image IS NULL';
            }
        }

        $where = implode(' AND ', $conditions);

        $bulletin = new Storage_Bulletin();

        $pager = $this->createPager($bulletin->getCount(
            null, $where, $whereArgs
        ));

        $page = $this->getParam('page');
        if ($page && !$pager->isValidPageNumber($page)) {
            $this->err404();
        }

        $pager->setCurrentPage($page);

        $comments = $bulletin->fetch(
            null,
            $where,
            $whereArgs,
            'created_at DESC',
            $pager->getOffset(),
            $pager->getItemsPerPage()
        );

        $existActiveComment = false;
        if (!empty($comments)) {
            foreach ($comments as $comment) {
                if ($comment['is_deleted'] === '0') {
                    $existActiveComment = true;
                    break;
                }
            }
        }

        $this->render('management/bulletin/index.php', get_defined_vars());
    }

    public function imageDelete()
    {
        $id = $this->getParam('comment_id');

        if (empty($id)) {
            $this->err400();
        }

        $bulletin = new Storage_Bulletin();
        $results  = $bulletin->fetch(
            null,
            'id = :id',
            ['id' => $id]
        );

        if (!isset($results[0]) || $results[0]['is_deleted'] === '1') {
            $this->err404();
        }

        $comment      = $results[0];
        $currentImage = $comment['image'];

        // study: 全体的にそうだけど、複数の処理を行なった時に後の処理で失敗した時のことが考えられてないです
        if (!empty($currentImage)) {
            $bulletin->deleteImageById($id);

            $uploader = $this->createImageUploader();
            $uploader->delete($currentImage, true);
        }

        $this->redirect('management/bulletin/index.php', $this->getSearchConditions());
    }

    public function delete()
    {
        $commentIds      = $this->getParam('comment_ids');
        $searchCondition = $this->getSearchConditions();

        if (count($commentIds) === 0) {
            $this->redirect('management/bulletin/index.php', $searchCondition);
        }

        $placeHolder = '';
        $whereArgs   = [];
        foreach ($commentIds as $key => $commentId) {
            $placeHolder           .= ":id_{$key},";
            $whereArgs["id_{$key}"] = $commentId;
        }

        $placeHolder = rtrim($placeHolder, ',');

        $bulletin = new Storage_Bulletin();

        $where    = "id IN ({$placeHolder}) AND image IS NOT NULL";
        $comments = $bulletin->fetch(null, $where, $whereArgs);

        if (count($comments) > 0) {
            $uploader = $this->createImageUploader();

            foreach ($comments as $comment) {
                $uploader->delete($comment['image'], true);
            }
        }

        $where = "id IN ({$placeHolder})";
        $bulletin->softDelete($where, $whereArgs);

        $this->redirect('management/bulletin/index.php', $searchCondition);
    }

    public function recovery()
    {
        $id = $this->getParam('comment_id');

        if (empty($id)) {
            $this->err400();
        }

        $bulletin = new Storage_Bulletin();
        $results  = $bulletin->fetch(
            null,
            'id = :id',
            ['id' => $id]
        );

        if (!isset($results[0]) || $results[0]['is_deleted'] === '0') {
            $this->err404();
        }

        $bulletin->recoveryById($id);

        $this->redirect('management/bulletin/index.php', $this->getSearchConditions());
    }

    protected function createPager($itemsCount)
    {
        $pager = new Pager(
            $itemsCount,
            self::PAGER_ITEMS_PER_PAGE,
            self::PAGER_WINDOW_SIZE
        );

        $pager->setUri($this->getEnv('Request-Uri'));

        return $pager;
    }

    protected function getSearchConditions()
    {
        $page = $this->getParam('page');

        if (empty($page)) {
            $page = 1;
        }

        return [
            'page'       => $page,
            'title'      => $this->getParam('title'),
            'body'       => $this->getParam('body'),
            'has_image'  => $this->getParam('has_image'),
            'is_deleted' => $this->getParam('is_deleted')
        ];
    }
}
