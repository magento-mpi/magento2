<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Adminhtml_Controller_Customer_Online extends Magento_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->_title(__('Customers Now Online'));

        if($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();

        $this->_setActiveMenu('Magento_Customer::customer_online');

        $this->_addBreadcrumb(__('Customers'), __('Customers'));
        $this->_addBreadcrumb(__('Online Customers'), __('Online Customers'));

        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Customer::online');
    }
}
