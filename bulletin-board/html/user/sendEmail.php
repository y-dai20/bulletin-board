<?php  include(HTML_FILES_DIR . '/common/header.php') ?>
<div id="contents">
  <h1>Membership Register</h1>

  <p>
    Thank you for your membership register.<br>
    We send confirmation e-mail to you. Please complete the registration by clicking the comfirmation URL.
  </p>
  <form class="submit" action="<?php echo get_uri('user/bulletin/index.php') ?>" method="post">
    <input type="submit" value="Back to top">
  </form>
</div>
<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
