/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   design
 * @package    enterprise_default
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
  * CMS JS Function
  */

function previewAction(formId, formObj, url){
    var formElem = $(formId);
    formElem.writeAttribute('target', '_blank');
    formObj.submit(url);
    formElem.writeAttribute('target', '');
}

function publishAction(publishUrl){
    setLocation(publishUrl);
}

function saveAndPublishAction(formObj, saveUrl){
    formObj.submit(saveUrl + 'back/publish/');
}

function dataChanged() {
    var buttonSaveAndPublish = $('save_publish_button');
    if (buttonSaveAndPublish && buttonSaveAndPublish.hasClassName('no-display')) {
        var buttonPublish = $('publish_button');
        if (buttonPublish) {
            buttonPublish.hide();
        }
        buttonSaveAndPublish.removeClassName('no-display');
    }
}

varienGlobalEvents.attachEventHandler('tinymceChange', dataChanged);

