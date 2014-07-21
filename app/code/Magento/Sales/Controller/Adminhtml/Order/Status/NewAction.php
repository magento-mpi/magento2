<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Status;

class NewAction extends \Magento\Sales\Controller\Adminhtml\Order\Status
{
    /**
     * New status form
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->_getSession()->getFormData(true);
        if ($data) {
            $status = $this->_objectManager->create('Magento\Sales\Model\Order\Status')->setData($data);
            $this->_coreRegistry->register('current_status', $status);
        }
        $this->_title->add(__('Order Status'));
        $this->_title->add(__('Create New Order Status'));
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Sales::system_order_statuses');
        $this->_view->renderLayout();
    }
}
