<?php include(HTML_FILES_DIR . '/common/header.php') ?>

<script type="text/javascript">
function submit_action_form(action, form_id) {
  var form = document.getElementById(form_id);
  form.setAttribute('action', action);
  form.submit();
}
</script>

<div id="contents">
  <?php if (!empty($loginUser)) :?>
  <form class="actionForm" action="<?php echo get_uri('user/logout.php') ?>" method="post">
    <div class="submit">
      <input type="hidden" name="page" value="<?php echo $pager->getCurrentPage() ?>" />
      <input type="submit" value="&raquo; Logout" />
    </div>
  </form>
  <?php else :?>
  <form class="actionForm" action="<?php echo get_uri('user/register.php') ?>" method="post">
    <div class="submit">
      <input type="hidden" name="page" value="<?php echo $pager->getCurrentPage() ?>" />
      <input type="submit" value="&raquo; Register" />
    </div>
  </form>
  <form class="actionForm" action="<?php echo get_uri('user/login.php') ?>" method="post">
    <div class="submit">
      <input type="hidden" name="page" value="<?php echo $pager->getCurrentPage() ?>" />
      <input type="submit" value="&raquo; Login" />
    </div>
  </form>
  <?php endif ?>

  <?php include(HTML_FILES_DIR . '/user/bulletin/form.php') ?>

  <?php if ($comments) : ?>
  <div class="comments">
    <?php foreach ($comments as $comment) : ?>
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
        <?php if ((!empty($loginUser) && $comment['user_id'] === $loginUser['id']) || (empty($loginUser) && empty($comment['user_id']))) :?>
        <form id="af<?php echo $comment['id'] ?>" class="actionForm" action="" method="post">
          <input type="hidden" name="comment_id" value="<?php echo $comment['id'] ?>" />
          <input type="hidden" name="page" value="<?php echo $pager->getCurrentPage() ?>" />
          <?php if (empty($loginUser)) :?>
            <input type="password" name="pass" value="" />
          <?php endif ?>
          <div class="submit">
            <input type="button" value="&raquo; DELETE" onclick="submit_action_form('<?php echo get_uri('user/bulletin/delete.php') ?>', 'af<?php echo $comment['id'] ?>');" />
            <input type="button" value="&raquo; EDIT" onclick="submit_action_form('<?php echo get_uri('user/bulletin/edit.php') ?>', 'af<?php echo $comment['id'] ?>');" />
          </div>
        </form>
        <?php endif ?>
      </div>
    <?php endforeach ?>
  </div>
  <?php endif ?>

  <?php include(HTML_FILES_DIR . '/common/pager.php') ?>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
