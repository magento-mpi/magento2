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
class Mage_Backend_Controller_Adminhtml_System extends Mage_Backend_Controller_ActionAbstract
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Mage_Adminhtml::system');
        $this->_addBreadcrumb(
            __('System'),
            __('System')
        );
        $this->renderLayout();
    }

    public function setStoreAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        if ($storeId) {
            $this->_session->setStoreId($storeId);
        }
        $this->_redirectReferer();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Adminhtml::system');
    }
}
