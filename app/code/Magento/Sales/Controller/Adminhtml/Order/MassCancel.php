<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order;

use \Magento\Backend\App\Action;

class MassCancel extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * Cancel selected orders
     *
     * @return void
     */
    public function execute()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countCancelOrder = 0;
        $countNonCancelOrder = 0;
        foreach ($orderIds as $orderId) {
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            if ($order->canCancel()) {
                $order->cancel()->save();
                $countCancelOrder++;
            } else {
                $countNonCancelOrder++;
            }
        }
        if ($countNonCancelOrder) {
            if ($countCancelOrder) {
                $this->messageManager->addError(__('%1 order(s) cannot be canceled.', $countNonCancelOrder));
            } else {
                $this->messageManager->addError(__('You cannot cancel the order(s).'));
            }
        }
        if ($countCancelOrder) {
            $this->messageManager->addSuccess(__('We canceled %1 order(s).', $countCancelOrder));
        }
        $this->_redirect('sales/*/');
    }
}
