<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wysiwyg controller for different purposes
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Cms_WysiwygController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Template directives callback
     *
     * TODO: move this to some model
     */
    public function directiveAction()
    {
        $directive = $this->getRequest()->getParam('___directive');
        $directive = Mage::helper('Mage_Core_Helper_Data')->urlDecode($directive);
        $url = Mage::getModel('Mage_Core_Model_Email_Template_Filter')->filter($directive);
        $image = $this->_objectManager->get('Mage_Core_Model_Image_AdapterFactory')->create();
        $response = $this->getResponse();
        try {
            $image->open($url);
            $response->setHeader('Content-Type', $image->getMimeType())->setBody($image->getImage());
        } catch (Exception $e) {
            $image->open(Mage::getSingleton('Mage_Cms_Model_Wysiwyg_Config')->getSkinImagePlaceholderUrl());
            $response->setHeader('Content-Type', $image->getMimeType())->setBody($image->getImage());
            Mage::logException($e);
        }
    }
}
