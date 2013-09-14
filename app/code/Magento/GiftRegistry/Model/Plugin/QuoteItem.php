<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GiftRegistry_Model_Plugin_QuoteItem
{
    /**
     * Copy gift registry item id flag from quote item to order item
     *
     * @param array $arguments
     * @param Magento_Code_Plugin_InvocationChain $invocationChain
     * @return Magento_Sales_Model_Order_Item|mixed
     */
    public function aroundItemToOrderItem(array $arguments, Magento_Code_Plugin_InvocationChain $invocationChain)
    {
        /** @var $orderItem Magento_Sales_Model_Order_Item */
        $orderItem = $invocationChain->proceed($arguments);
        /** @var $quoteItem Magento_Sales_Model_Quote_Item */
        $quoteItem = reset($arguments);

        if ($quoteItem instanceof Magento_Sales_Model_Quote_Address_Item) {
            $registryItemId = $quoteItem->getQuoteItem()->getGiftregistryItemId();
        } else {
            $registryItemId = $quoteItem->getGiftregistryItemId();
        }

        if ($registryItemId) {
            $orderItem->setGiftregistryItemId($registryItemId);
        }
        return $orderItem;
    }

}