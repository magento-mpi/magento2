<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Edit;

class Index extends \Magento\Sales\Controller\Adminhtml\Order\Create\Index
{
    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::actions_edit');
    }

    /**
     * Index page
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Orders'));
        $this->_title->add(__('Edit Order'));
        $this->_view->loadLayout();

        $this->_initSession()->_setActiveMenu('Magento_Sales::sales_order');
        $this->_view->renderLayout();
    }
}
