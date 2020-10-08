<?php include(HTML_FILES_DIR . '/common/header.php') ?>

<div id="contents">
  <?php include(HTML_FILES_DIR . '/common/error.php') ?>

  <div class="comments">
    <div class="comment">
      <div class="name">
        <?php if (!empty($comment['name']) ) :?>
          <?php echo h($comment['name']) ?>
          <?php if (!empty($comment['user_id'])) echo "[ID:{$comment['user_id']}]" ?>
        <?php else :?>
          No Name
          <?php if (!empty($comment['user_id'])) echo "[ID:{$comment['user_id']}]" ?>
        <?php endif ?>
      </div>
      <div class="title">
        <?php echo h($comment['title']) ?>
      </div>
      <div class="body">
        <?php echo nl2br(h($comment['body'])) ?>
      </div>
      <?php if (!empty($comment['image']) && file_exists(PROJECT_ROOT . '/' . "{$imageDir}/{$comment['image']}")) : ?>
      <div class="photo">
        <a href="<?php echo get_uri("{$imageDir}/{$comment['image']}") ?>" target="_blank">
          <img src="<?php echo get_uri("{$imageDir}/{$comment['image']}") ?>" />
        </a>
      </div>
      <?php endif ?>
      <div class="date">
        <?php echo date('d-m-Y H:i', strtotime($comment['created_at'])) ?>
      </div>
    </div>
  </div>
  <div class="confirmForm">
    <?php if (!empty($comment['pass']) || (!empty($loginUser) &&  $loginUser['id'] === $comment['user_id'])) : ?>
      <form class="default" action="<?php echo get_uri('user/bulletin/delete.php') ?>" method="post">
        <input type="hidden" name="comment_id" value="<?php echo $comment['id'] ?>" />
        <input type="hidden" name="page" value="<?php echo h($page) ?>" />
        <input type="hidden" name="pass" value="<?php echo h($pass) ?>" />
        <?php if (empty($errors)) : ?>
          <div class="message">
            Are you sure ?
          </div>
          <div class="submit">
            <input type="hidden" name="do_delete" value="1" />
            <input type="submit" value="&raquo; DELETE" />
            <input type="button" value="&raquo; CANCEL" onclick="window.location.href='<?php echo get_uri('user/bulletin/index.php') ?>?page=<?php echo h($page) ?>';">
          </div>
        <?php else : ?>
          <div class="submit">
            <input type="password" name="pass" value="<?php echo h($pass) ?>" />
            <input type="submit" value="&raquo; DELETE" />
          </div>
        <?php endif ?>
      </form>
    <?php else : ?>
      <form class="default" action="<?php echo get_uri('user/bulletin/index.php') ?>" method="get">
        <div class="message">
          This comment can't be deleted.
        </div>
        <div class="submit">
          <input type="hidden" name="page" value="<?php echo h($page) ?>" />
          <input type="submit" value="&raquo; BACK">
        </div>
      </form>
    <?php endif ?>
  </div>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
