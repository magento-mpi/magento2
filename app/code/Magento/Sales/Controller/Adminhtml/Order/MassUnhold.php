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

class MassUnhold extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * Unhold selected orders
     *
     * @return void
     */
    public function execute()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countUnHoldOrder = 0;
        $countNonUnHoldOrder = 0;

        foreach ($orderIds as $orderId) {
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            if ($order->canUnhold()) {
                $order->unhold()->save();
                $countUnHoldOrder++;
            } else {
                $countNonUnHoldOrder++;
            }
        }
        if ($countNonUnHoldOrder) {
            if ($countUnHoldOrder) {
                $this->messageManager->addError(
                    __('%1 order(s) were not released from on hold status.', $countNonUnHoldOrder)
                );
            } else {
                $this->messageManager->addError(__('No order(s) were released from on hold status.'));
            }
        }
        if ($countUnHoldOrder) {
            $this->messageManager->addSuccess(
                __('%1 order(s) have been released from on hold status.', $countUnHoldOrder)
            );
        }
        $this->_redirect('sales/*/');
    }
}
