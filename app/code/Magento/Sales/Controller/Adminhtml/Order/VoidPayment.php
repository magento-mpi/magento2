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

class VoidPayment extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * Attempt to void the order payment
     *
     * @return void
     */
    public function execute()
    {
        if (!($order = $this->_initOrder())) {
            return;
        }
        try {
            $order->getPayment()->void(new \Magento\Framework\Object()); // workaround for backwards compatibility
            $order->save();
            $this->messageManager->addSuccess(__('The payment has been voided.'));
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We couldn\'t void the payment.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
        $this->_redirect('sales/*/view', array('order_id' => $order->getId()));
    }
}
