<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Status;

class Index extends \Magento\Sales\Controller\Adminhtml\Order\Status
{
    /**
     * Statuses grid page
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Order Status'));
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Sales::system_order_statuses');
        $this->_view->renderLayout();
    }
}
