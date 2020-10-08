<?php  include(HTML_FILES_DIR . '/common/header.php') ?>

<script type="text/javascript" src="<?php echo get_uri('js/manage.js') ?>"></script>
<form class="actionForm" action="<?php echo get_uri('management/logout.php') ?>" method="post">
  <div class="submit">
    <input type="submit" value="&raquo; Logout" />
  </div>
</form>
<div class="search_form">
  <form class="default" action="<?php echo get_uri('management/bulletin/index.php') ?>" method="get">
    <div class="item">
      <p class="title">
        Title
      </p>
      <p class="input">
        <input type="text" name="title" value="<?php echo h($title) ?>">
      </p>
    </div>
    <div class="item">
      <p class="title">
        Body
      </p>
      <p class="input">
        <input type="text" name="body" value="<?php echo h($body) ?>">
      </p>
    </div>
    <div class="item">
      <p class="title">
        Image
      </p>
      <input type="radio" name="has_image" value="1" <?php if ($hasImage === '1') echo 'checked' ?>>with
      <input type="radio" name="has_image" value="0" <?php if ($hasImage === '0') echo 'checked' ?>>without
      <input type="radio" name="has_image" value=""  <?php if (is_empty($hasImage)) echo 'checked' ?>>unspecified
    </div>
    <div class="item">
      <p class="title">
        Status
      </p>
      <input type="radio" name="is_deleted" value="0" <?php if ($isDeleted === '0') echo 'checked' ?>>On
      <input type="radio" name="is_deleted" value="1" <?php if ($isDeleted === '1') echo 'checked' ?>>delete
      <input type="radio" name="is_deleted" value=""  <?php if (is_empty($isDeleted)) echo 'checked' ?>>unspecified
    </div>
    <div class="submit">
      <input type="submit" value="&raquo; Search">
    </div>
  </form>
</div>
<table>
  <tr>
    <th class="check">
      <input type="checkbox" id="js_all_check" onclick="allChecked(this);">
    </th>
    <th class="id">ID</th>
    <th>Title</th>
    <th>Body</th>
    <th>Image</th>
    <th>Date</th>
    <th class="button"></th>
  </tr>
  <?php if (!empty($comments)) : ?>
    <form id="comments" method="post">
      <?php foreach ($comments as $comment) : ?>
        <tr class="<?php if ($comment['is_deleted']) echo 'deleted_item' ?>">
          <td>
            <?php if ($comment['is_deleted'] === '0') : ?>
              <input type="checkbox" name="comment_ids[]" id="checkbox_<?php echo $comment['id'] ?>" value="<?php echo $comment['id'] ?>" onclick="singleChecked(this);">
            <?php endif ?>
          </td>
          <td><?php echo $comment['id'] ?></td>
          <td class="title"><?php echo h($comment['title']) ?></td>
          <td class="body"><?php echo nl2br(h($comment['body'])) ?></td>
          <td>
            <?php if (!empty($comment['image']) && file_exists(PROJECT_ROOT . '/' . "{$imageDir}/{$comment['image']}")) : ?>
              <div class="photo_area">
                <div class="photo">
                  <a href="<?php echo get_uri("{$imageDir}/{$comment['image']}") ?>" target="_blank">
                    <img src="<?php echo get_uri("{$imageDir}/{$comment['image']}") ?>" />
                  </a>
                </div>
                <div class="submit">
                  <button class="btn" name="comment_id" value="<?php echo $comment['id'] ?>" formaction="<?php echo get_uri('management/bulletin/imageDelete.php') ?>" onclick="return deleteConfirm()">&raquo; DEL</button>
                </div>
              </div>
            <?php endif ?>
          </td>
          <td><?php echo date('d-m-Y H:i', strtotime($comment['created_at'])) ?></td>
          <td>
            <div class="submit">
              <?php if ($comment['is_deleted'] === '0') : ?>
                <input type="button" value="&raquo; DEL" onclick="singleDelete('checkbox_<?php echo $comment['id'] ?>', '<?php echo get_uri('management/bulletin/delete.php') ?>')" >
              <?php elseif ($comment['is_deleted'] === '1') : ?>
                <button class="btn" name="comment_id" value="<?php echo $comment['id'] ?>" formaction="<?php echo get_uri('management/bulletin/recovery.php') ?>">&raquo; REC</button>
              <?php endif ?>
            </div>
          </td>
        </tr>
      <?php endforeach ?>
    <?php endif ?>
  </table>
  <input type="hidden" name="page" value="<?php echo $pager->getCurrentPage() ?>">
  <input type="hidden" name="title" value="<?php echo h($title) ?>">
  <input type="hidden" name="body" value="<?php echo h($body) ?>">
  <input type="hidden" name="has_image" value="<?php echo h($hasImage) ?>">
  <input type="hidden" name="is_deleted" value="<?php echo h($isDeleted) ?>">
  <?php if ($existActiveComment) : ?>
    <div class="btn_space">
      <button class="btn" formaction="<?php echo get_uri('management/bulletin/delete.php') ?>" onclick="return deleteConfirm()">&raquo; Delete Checked Items</button>
    </div>
  <?php endif ?>
</form>

<?php include(HTML_FILES_DIR . '/common/pager.php') ?>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
