<?php include(HTML_FILES_DIR . '/common/header.php') ?>

<div id="contents">
  <h2>403 Forbidden</h2>
  <div>
    You don't have permission to access <?php if (isset($requestUri)) echo $requestUri ?> on this server.<br />
    <?php if ($message) : ?>
      <?php echo $message ?>
    <?php endif ?>
  </div>
</div>

<?php include(HTML_FILES_DIR . '/common/footer.php') ?>
