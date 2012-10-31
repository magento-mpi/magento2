/**
 * {license_notice}
 *
 * @category    design
 * @package     default_default
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*function previewAction(formId, formObj, url){
    var formElem = $(formId);
    var previewWindowName = 'cms-page-preview-' + $('page_page_id').value;

    formElem.writeAttribute('target', previewWindowName);
    formObj.submit(url);
    formElem.writeAttribute('target', '');
}*/

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
