<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wysiwyg controller for different purposes
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Cms_Wysiwyg extends Magento_Adminhtml_Controller_Action
{
    /**
     * Template directives callback
     *
     * TODO: move this to some model
     */
    public function directiveAction()
    {
        $directive = $this->getRequest()->getParam('___directive');
        $directive = $this->_objectManager->get('Magento_Core_Helper_Data')->urlDecode($directive);
        $url = Mage::getModel('Magento_Core_Model_Email_Template_Filter')->filter($directive);
        $image = $this->_objectManager->get('Magento_Core_Model_Image_AdapterFactory')->create();
        $response = $this->getResponse();
        try {
            $image->open($url);
            $response->setHeader('Content-Type', $image->getMimeType())->setBody($image->getImage());
        } catch (Exception $e) {
            $image->open(Mage::getSingleton('Magento_Cms_Model_Wysiwyg_Config')->getSkinImagePlaceholderUrl());
            $response->setHeader('Content-Type', $image->getMimeType())->setBody($image->getImage());
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
    }
}
