<?php include(HTML_FILES_DIR . '/common/header.php') ?>

<div id="contents">
  <h3>Membership Register</h3>

  <?php include(HTML_FILES_DIR . '/common/error.php') ?>

  <?php if (empty($errors)) : ?>
    <p>Thank you for your registration. Membership is now complete.</p>
  <?php endif ?>
  <form class="submit" action="<?php echo get_uri('user/bulletin/index.php') ?>" method="post">
    <input type="submit" value="Back to top">
  </form>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
