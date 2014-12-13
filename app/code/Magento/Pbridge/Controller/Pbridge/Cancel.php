<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Controller\Pbridge;

class Cancel extends \Magento\Pbridge\Controller\Pbridge
{
    /**
     * Review success action
     *
     * @return void
     */
    public function execute()
    {
        try {
            // if there is an order - cancel it
            $orderId = $this->_checkoutSession->getLastOrderId();
            /** @var \Magento\Sales\Model\Order $order */
            $order = $orderId ? $this->_orderFactory->create()->load($orderId) : false;
            if ($order && $order->getId() && $order->getQuoteId() == $this->_checkoutSession->getQuoteId()) {
                $order->cancel()->save();
                $this->_checkoutSession
                    ->unsLastQuoteId()
                    ->unsLastSuccessQuoteId()
                    ->unsLastOrderId()
                    ->unsLastRealOrderId()
                    ->addSuccess(__('Order has been canceled.'));
            } else {
                $this->_checkoutSession->addSuccess(__('Order has been canceled.'));
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->_checkoutSession->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_checkoutSession->addError(__('Unable to cancel order.'));
            $this->_logger->logException($e);
        }

        $this->_initActionLayout();
        $this->_view->renderLayout();
    }
}
