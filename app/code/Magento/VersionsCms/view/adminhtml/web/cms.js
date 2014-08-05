/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require([
  "prototype",
  "mage/adminhtml/events"
], function(){

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

  window.publishAction = publishAction;
  window.dataChanged = dataChanged;
});