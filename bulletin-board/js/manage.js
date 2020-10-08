function singleDelete(checkbox_id, action)
{
  if (!deleteConfirm()) {
    return false;
  }

  toggleChecked(false);

  document.getElementById(checkbox_id).checked = true;

  var form = document.getElementById('comments');
  form.action = action;
  form.submit();
}

function allChecked(checkbox) {
  toggleChecked(checkbox.checked);
}

function singleChecked(checkbox) {
  var all_check   = document.getElementById('js_all_check');
  var comment_ids = document.getElementsByName('comment_ids[]');

  if (!checkbox.checked) {
    all_check.checked = false;
    return;
  }

  for (var i = 0; i < comment_ids.length; i++) {
    if (!comment_ids[i].checked) {
      all_check.checked = false;
      return;
    }
  }

  all_check.checked = true;
}

function deleteConfirm()
{
  return confirm('Are you sure you want to delete?');
}

function toggleChecked(check)
{
  var comment_ids = document.getElementsByName('comment_ids[]');

  for (var i = 0; i < comment_ids.length; i++) {
    comment_ids[i].checked = check;
  }
}
