<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GiftMessage_Model_Plugin_QuoteItem
{
    /** @var \Magento_GiftMessage_Helper_Message */
    protected $_helper;

    /** @var \Magento_GiftMessage_Model_MessageFactory */
    protected $_messageFactory;

    public function __construct(
        Magento_GiftMessage_Helper_Message $helper,
        Magento_GiftMessage_Model_MessageFactory $messageFactory
    ) {
        $this->_helper = $helper;
        $this->_messageFactory = $messageFactory;
    }

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

        $isAvailable = $this->_helper->isMessagesAvailable(
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
            $giftMessage = $this->_messageFactory->create()->load($giftMessageId)
                ->setId(null)
                ->save();
            $quoteItem->setGiftMessageId($giftMessage->getId());
        }

        return $orderItem;
    }
}
