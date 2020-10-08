<?php  include(HTML_FILES_DIR . '/common/header.php') ?>

<div id="contents">
  <?php include(HTML_FILES_DIR . '/common/error.php') ?>

  <form class="default" action="<?php echo get_uri('management/login.php') ?>" method="post">
    <div class="item">
      <p class="id">
        ID
      </p>
      <input type="text" name="login_id" value="<?php if (isset($loginId)) echo h($loginId) ?>" />
    </div>
    <div class="item">
      <p class="item">
        Password
      </p>
      <input type="password" name="pass" value="<?php if (isset($pass)) echo h($pass) ?>" />
    </div>
    <div class="submit">
      <input type="hidden" name="do_login" value="1">
      <input type="submit" value="LOGIN">
    </div>
  </form>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
