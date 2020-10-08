<?php

class Controller_User_Bulletin extends Controller_User_Application
{
    const PAGER_ITEMS_PER_PAGE = 10;
    const PAGER_WINDOW_SIZE    = 5;

    public function __construct()
    {
        $this->imageDir = Uploader_File::UPLOAD_DIR_NAME . '/bulletin';
    }

    public function index()
    {
        $loginUser = $this->loginUser;
        if (!empty($loginUser)) {
            $name = $loginUser['name'];
        }

        $bulletin = new Storage_Bulletin();

        $pager = $this->createPager($bulletin->getCount(
            null, 'is_deleted = :is_deleted', ['is_deleted' => 0]
        ));

        $page = $this->getParam('page');
        if ($page && !$pager->isValidPageNumber($page)) {
            $this->err404();
        }

        $pager->setCurrentPage($page);

        $comments = $bulletin->fetch(
            null,
            'is_deleted = :is_deleted',
            ['is_deleted' => 0],
            'created_at DESC',
            $pager->getOffset(),
            $pager->getItemsPerPage()
        );

        // study: get_defined_vars() / extract() / compact()
        //        これらのメソッドはあまりよくないメソッドです。
        //        なぜか？ 不要な変数も含めて全部使われてしまうので、不要なものと必要なものの区別が付かなくなる
        //        単発のコードならいいけど、継続的に触るコードだと要らなくなった変数を消す時に苦労することになる
        $this->render('user/bulletin/index.php', get_defined_vars());
    }

    public function post()
    {
        $name  = $this->getParam('name');
        $title = $this->getParam('title');
        $body  = $this->getParam('body');
        $pass  = $this->getParam('pass');

        $data = [
            'name'  => $name,
            'title' => $title,
            'body'  => $body,
            'pass'  => $pass,
        ];

        $uploader = $this->createImageUploader();
        $image    = $this->getFile('image');
        $hasImage = !empty($image);

        if ($hasImage) {
            $data['image'] = $image;
        }

        $bulletin = new Storage_Bulletin();
        $errors   = $bulletin->validate($data);

        if (empty($errors)) {
            if ($hasImage) {
                $data['image'] = $uploader->uploadImage($image['data']);
            } else {
                $data['image'] = null;
            }

            $loginUser = $this->loginUser;
            if (!empty($loginUser)) {
                $data['user_id'] = $loginUser['id'];
            }

            $bulletin->insert($data);

            $this->redirect('user/bulletin/index.php');
        } else {
            $this->render('user/bulletin/post.php', get_defined_vars());
        }
    }

    public function edit()
    {
        $id   = $this->getParam('comment_id');
        $pass = $this->getParam('pass');
        $page = $this->getParam('page');

        if (empty($id)) {
            $this->err400();
        }

        if (empty($page)) {
            $page = 1;
        }

        $bulletin = new Storage_Bulletin();
        $results  = $bulletin->fetch(null, 'id = :id', ['id' => $id]);

        if (!isset($results[0]) || $results[0]['is_deleted'] === '1') {
            $this->err404();
        }

        $errors  = [];
        $comment = $results[0];

        $name         = $comment['name'];
        $title        = $comment['title'];
        $body         = $comment['body'];
        $currentImage = $comment['image'];

        $isEditForm      = true;
        $isPasswordMatch = false;

        $loginUser = $this->loginUser;

        if (!empty($loginUser) && $loginUser['id'] !== $comment['user_id']) {
            $this->err403();
        }

        if (empty($loginUser)) {
            if (empty($comment['pass'])) {
                $this->render('user/bulletin/edit.php', get_defined_vars());
                return;
            }

            if (empty($pass)) {
                $errors[] = 'password is empty.';
            } else {
                if ($bulletin->verifyPassword($pass, $comment['pass'])) {
                    $isPasswordMatch = true;
                } else {
                    $errors[] = 'The password you entered, do not match.';
                }
            }
        }

        if (empty($errors) && $this->getParam('do_edit') === '1') {
            $name  = $this->getParam('name');
            $title = $this->getParam('title');
            $body  = $this->getParam('body');

            $data = [
                'name'  => $name,
                'title' => $title,
                'body'  => $body,
            ];

            $doDeleteImage = ($this->getParam('del_image') === '1');

            $uploader = $this->createImageUploader();
            $image    = $this->getFile('image');
            $hasImage = !empty($image);

            if (!$doDeleteImage && $hasImage) {
                $data['image'] = $image;
            }

            $bulletin = new Storage_Bulletin();
            $errors   = $bulletin->validate($data);

            if (empty($errors)) {
                if ($doDeleteImage && !empty($currentImage)) {
                    $uploader->delete($currentImage, true);
                    $data['image'] = null;
                } elseif (!$doDeleteImage && $hasImage) {
                    $data['image'] = $uploader->uploadImage($image['data']);
                }

                $bulletin->updateById($id, $data);

                $this->redirect('user/bulletin/index.php', ['page' => $page]);
            }
        }

        $this->render('user/bulletin/edit.php', get_defined_vars());
    }

    public function delete()
    {
        $id   = $this->getParam('comment_id');
        $pass = $this->getParam('pass');
        $page = $this->getParam('page');

        if (empty($id)) {
            $this->err400();
        }

        if (empty($page)) {
            $page = 1;
        }

        $bulletin = new Storage_Bulletin();
        $results  = $bulletin->fetch(null, 'id = :id', ['id' => $id]);

        if (!isset($results[0]) || $results[0]['is_deleted'] === '1') {
            $this->err404();
        }

        $errors  = [];
        $comment = $results[0];

        $loginUser = $this->loginUser;

        if (!empty($loginUser) && $loginUser['id'] !== $comment['user_id']) {
            $this->err403();
        }

        if (empty($loginUser)) {
            if (empty($comment['pass'])) {
                $this->render('user/bulletin/delete.php', get_defined_vars());
                return;
            }

            if (empty($pass)) {
                $errors[] = 'password is empty.';
            } elseif (!$bulletin->verifyPassword($pass, $comment['pass'])) {
                $errors[] = 'The password you entered, do not match.';
            }
        }

        if (!empty($errors) || $this->getParam('do_delete') !== '1') {
            $this->render('user/bulletin/delete.php', get_defined_vars());
            return;
        }

        if (!empty($comment['image'])) {
            $uploader = $this->createImageUploader();
            $uploader->delete($comment['image'], true);
        }

        $bulletin->softDelete('id = :id', ['id' => $id]);

        $pager = $this->createPager($bulletin->getCount(
            null, 'is_deleted = :is_deleted', ['is_deleted' => 0]
        ));

        $page = min($page, $pager->getTotalPage());

        $this->redirect('user/bulletin/index.php', ['page' => $page]);
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
}
