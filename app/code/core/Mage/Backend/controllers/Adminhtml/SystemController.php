<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System admin controller
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Adminhtml_SystemController extends Mage_Backend_Controller_ActionAbstract
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Mage_Adminhtml::system');
        $this->_addBreadcrumb(
            Mage::helper('Mage_Backend_Helper_Data')->__('System'),
            Mage::helper('Mage_Backend_Helper_Data')->__('System')
        );
        $this->renderLayout();
    }

    public function setStoreAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        if ($storeId) {
            Mage::getSingleton('Mage_Backend_Model_Session')->setStoreId($storeId);
        }
        $this->_redirectReferer();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Adminhtml::system');
    }
}