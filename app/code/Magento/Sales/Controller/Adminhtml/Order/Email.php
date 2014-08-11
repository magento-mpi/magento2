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

class Email extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * Notify user
     *
     * @return void
     */
    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                /** @var \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender */
                $orderSender = $this->_objectManager->create(
                    'Magento\Sales\Model\Order\Email\Sender\OrderSender'
                );
                $orderSender->send($order);

                $historyItem = $this->_objectManager->create(
                    'Magento\Sales\Model\Resource\Order\Status\History\Collection'
                )->getUnnotifiedForInstance(
                    $order,
                    \Magento\Sales\Model\Order::HISTORY_ENTITY_NAME
                );
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }
                $this->messageManager->addSuccess(__('You sent the order email.'));
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We couldn\'t send the email order.'));
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            }
        }
        $this->_redirect('sales/order/view', array('order_id' => $order->getId()));
    }
}
