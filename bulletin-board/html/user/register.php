<?php  include(HTML_FILES_DIR . '/common/header.php') ?>

<div id="contents">
  <?php include(HTML_FILES_DIR . '/common/error.php') ?>

  <?php if ($doConfirm === "1" && empty($errors)) : ?>
    <form class="default" action="<?php echo get_uri('user/register.php') ?>" method="post">
      <div class="item">
        <p class="name">
          Name
        </p>
        <p class='input'><?php echo h($name) ?></p>
      </div>
      <div class="item">
        <p class='email'>
          E-mail
        </p>
        <p class='input'><?php echo h($email) ?></p>
      </div>
      <div class="submit">
        <input type="hidden" name="name" value="<?php echo h($name) ?>" />
        <input type="hidden" name="email" value="<?php echo h($email) ?>" />
        <input type="hidden" name="pass" value="<?php echo h($pass) ?>" />
        <input type="submit" value="&raquo; Back" formaction="<?php echo get_uri('user/register.php') ?>"/>
        <input type="submit" name="do_register" value="&raquo; Submit" />
      </div>
    </form>
  <?php else : ?>
    <form class="default" action="" method="post">
      <div class="item">
        <p class='name'>
          Name
        </p>
        <p class='input'>
          <input type="text" name="name" value="<?php if (isset($name)) echo h($name) ?>" />
        </p>
      </div>
      <div class="item">
        <p class='email'>
          E-mail
        </p>
        <p class='input'>
          <input type="text" name="email" value="<?php if (isset($email)) echo h($email) ?>" />
        </p>
      </div>
      <div class="item">
        <p class='pass'>
          Password
        </p>
        <p class='input'>
          <input type="password" name="pass" value="<?php if (isset($pass)) echo h($pass) ?>" />
        </p>
      </div>
      <div class="submit">
        <input type="hidden" name="do_confirm" value="1">
        <input type="button" value="&raquo; Back" onclick="window.location.href = '<?php echo get_uri('user/bulletin/index.php') ?>';" />
        <input type="submit" value="&raquo; Confirm" formaction="<?php echo get_uri('user/register.php') ?>"/>
      </div>
    </form>
  <?php endif ?>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
