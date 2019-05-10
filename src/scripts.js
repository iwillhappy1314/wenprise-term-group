jQuery(document).ready(function($) {

  function wprs_term_group_check_select() {
    var val_primary = $('#_wprs_is_primary:checked').val(),
        val_parent = $('#_wprs_parent_term').val(),
        condition_primary = $('#_wprs_is_primary').closest('.form-field'),
        condition_parent = $('#_wprs_parent_term').closest('.form-field');

    console.log(val_primary);
    console.log(val_parent);

    // 根据是否需要发票显示
    if (val_primary) {
      condition_parent.hide();
    } else {
      condition_parent.show();
    }

    if (val_parent) {
      condition_primary.hide();
    } else {
      condition_primary.show();
    }
  }

  wprs_term_group_check_select();

  $('#_wprs_is_primary, #_wprs_parent_term').change(function() {
    wprs_term_group_check_select();
  });

});