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
      btn.removeClass('mage-disabled').addClass('mage-enabled');
      btn.removeAttr('disabled');
    }else{
      btn.removeClass('mage-enabled').addClass('mage-disabled');
      btn.attr('disabled', 'disabled');
    }
  });
});
