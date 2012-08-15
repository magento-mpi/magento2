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
 * Media library upload controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Media_UploaderController extends Mage_Adminhtml_Controller_Action
{

    public function uploadAction()
    {
        $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode($_REQUEST));
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Media_Uploader')
        );
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')
            ->isAllowed(Mage_Backend_Model_Acl_Config::ACL_RESOURCE_ALL);
    }
}
