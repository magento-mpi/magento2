<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GiftMessage_Model_Plugin_QuoteItem
{
    /**
     * @param array $arguments
     * @param Magento_Code_Plugin_InvocationChain $invocationChain
     * @return Magento_Sales_Model_Order_Item|mixed
     */
    public function aroundItemToOrderItem(array $arguments, Magento_Code_Plugin_InvocationChain $invocationChain)
    {
        /** @var $orderItem Magento_Sales_Model_Order_Item */
        $orderItem = $invocationChain->proceed($arguments);

        // Do not import giftmessage data if order is reordered
        $order = $orderItem->getOrder();
        if ($order && $order->getReordered()) {
            return $orderItem;
        }

        $isAvailable = Mage::helper('Magento_GiftMessage_Helper_Message')->isMessagesAvailable(
            'order_item',
            $orderItem,
            $orderItem->getStoreId()
        );
        if (!$isAvailable) {
            return $orderItem;
        }

        /** @var $quoteItem Magento_Sales_Model_Quote_Item */
        $quoteItem = reset($arguments);
        if ($giftMessageId = $orderItem->getGiftMessageId()) {
            $giftMessage = Mage::getModel('Magento_GiftMessage_Model_Message')->load($giftMessageId)
                ->setId(null)
                ->save();
            $quoteItem->setGiftMessageId($giftMessage->getId());
        }

        return $orderItem;
    }
}
