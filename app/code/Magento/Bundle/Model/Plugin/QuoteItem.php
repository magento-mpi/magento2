<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Bundle_Model_Plugin_QuoteItem
{
    /**
     * Add bundle attributes to order data
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

        if ($attributes = $quoteItem->getProduct()->getCustomOption('bundle_selection_attributes')) {
            $productOptions = $orderItem->getProductOptions();
            $productOptions['bundle_selection_attributes'] = $attributes->getValue();
            $orderItem->setProductOptions($productOptions);
        }
        return $orderItem;
    }
}