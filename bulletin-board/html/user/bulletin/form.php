<?php include(HTML_FILES_DIR . '/common/error.php') ?>

<?php $_action = (isset($isEditForm)) ? 'user/bulletin/edit.php' : 'user/bulletin/post.php' ?>
<form class="default" action="<?php echo get_uri($_action) ?>" method="post" enctype="multipart/form-data">
  <div class="item">
    <p class="title">
      Name (Optional)
    </p>
    <p class="input">
      <input type="text" name="name" value="<?php if (isset($name)) echo h($name) ?>">
    </p>
  </div>
  <div class="item">
    <p class="title">
      Title
    </p>
    <p class="input">
      <input type="text" name="title" value="<?php if (isset($title)) echo h($title) ?>" />
    </p>
  </div>
  <div class="item">
    <p class="title">
      Body
    </p>
    <p class="input">
      <textarea style="height: 80px;" name="body"><?php if (isset($body)) echo h($body) ?></textarea>
    </p>
  </div>
  <div class="item">
    <p class="title">
      Photo (Optional)
    </p>
    <p class="input">
      <input type="file" name="image" />
    </p>
  </div>
  <?php if (isset($isEditForm)) : ?>
    <?php if (!empty($currentImage)) : ?>
    <div class="item">
      <p class="title">
        Current Photo
      </p>
      <p class="input">
        <img class="photo" src="<?php echo get_uri("{$imageDir}/{$currentImage}") ?>" /><br />
        <input id="cpd" type="checkbox" name="del_image" value="1" />
        <label for="cpd">Delete Current Photo</label>
      </p>
    </div>
    <?php endif ?>
    <div class="submit">
      <input type="hidden" name="do_edit" value="1" />
      <input type="hidden" name="comment_id" value="<?php if (isset($id)) echo $id ?>" />
      <input type="hidden" name="page" value="<?php if (isset($page)) echo h($page) ?>" />
      <input type="hidden" name="pass" value="<?php if (isset($pass)) echo h($pass) ?>" />
      <input type="submit" value="&raquo; EDIT" />
      <input type="button" value="&raquo; CANCEL" onclick="window.location.href='<?php echo get_uri('user/bulletin/index.php') ?>?page=<?php echo h($page) ?>';">
    </div>
  <?php else : ?>
    <?php if (empty($loginUser)) :?>
    <div class="item">
      <p class="title">
        Password (Optional)
      </p>
      <p class="input">
          <input type="password" name="pass" value="<?php if (isset($pass)) echo h($pass) ?>" />
      </p>
    </div>
    <?php endif ?>
    <div class="submit">
      <input type="submit" value="&raquo; POST COMMENT" />
    </div>
  <?php endif ?>
</form>
