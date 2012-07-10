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
      btn.removeClass('mage-install-disabled').addClass('mage-install-enabled');
      btn.removeAttr('disabled');
    }else{
      btn.removeClass('mage-install-enabled').addClass('mage-install-disabled');
      btn.attr('disabled', 'disabled');
    }
  });
});
