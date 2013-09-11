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
     * @var Magento_GiftMessage_Helper_Message
     */
    protected $_helper;

    /**
     * @param Magento_GiftMessage_Helper_Message $helper
     */
    public function __construct(
        Magento_GiftMessage_Helper_Message $helper
    ) {
        $this->_helper = $helper;
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
        $quoteItem = reset($arguments);

        $isAvailable = $this->_helper->isMessagesAvailable(
            'item',
            $quoteItem,
            $quoteItem->getStoreId()
        );

        $orderItem->setGiftMessageId($quoteItem->getGiftMessageId());
        $orderItem->setGiftMessageAvailable($isAvailable);
        return $orderItem;
    }
}
