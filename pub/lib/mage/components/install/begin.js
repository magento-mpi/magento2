/**
 * {license_notice}
 *
 * @category    mage install begin
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
$(document).ready(function() {
  $('#agree').on('click', function(){
    var btn = $('#submitButton');
    if(this.checked){
      btn.removeClass('disabled').addClass('enabled');
      btn.removeAttr('disabled');
    }else{
      btn.removeClass('enabled').addClass('disabled');
      btn.attr('disabled', 'disabled');
    }
  });
});
