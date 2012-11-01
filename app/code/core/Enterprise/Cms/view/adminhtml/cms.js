/**
 * {license_notice}
 *
 * @category    design
 * @package     default_default
 * @copyright   {copyright}
 * @license     {license_link}
 */
function publishAction(publishUrl){
    setLocation(publishUrl);
}

function dataChanged() {
   $$('p.form-buttons button.publish').each(function(e){
      var isVisible = e.style.display != 'none' && !$(e).hasClassName('no-display');
      
      if(e.id == 'publish_button' && isVisible) {
          e.style.display = 'none';
      } else if(!isVisible && e.id == 'save_publish_button') {
          e.style.display = '';
          $(e).removeClassName('no-display');
      }
   })
}

varienGlobalEvents.attachEventHandler('tinymceChange', dataChanged);
