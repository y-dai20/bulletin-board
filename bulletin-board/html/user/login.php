<?php  include(HTML_FILES_DIR . '/common/header.php') ?>

<div id="contents">
  <?php include(HTML_FILES_DIR . '/common/error.php') ?>

  <form class="default" action="<?php echo get_uri('user/login.php') ?>" method="post">
     <div class="item">
       <p class="email">
         E-mail
       </p>
       <input type="text" name="email" value="<?php if (isset($email)) echo h($email) ?>" />
     </div>
     <div class="item">
       <p class="password">
         Password
       </p>
       <input type="password" name="pass" value="<?php if (isset($pass)) echo h($pass) ?>" />
     </div>
     <div class="submit">
       <input type="hidden" name="do_login" value="1">
       <input type="submit" value="&raquo; Submit">
     </div>
  </form>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
