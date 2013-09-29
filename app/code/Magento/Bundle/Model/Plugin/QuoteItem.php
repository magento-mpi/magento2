<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model\Plugin;

class QuoteItem
{
    /**
     * Add bundle attributes to order data
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Sales\Model\Order\Item|mixed
     */
    public function aroundItemToOrderItem(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $invocationChain->proceed($arguments);
        /** @var $quoteItem \Magento\Sales\Model\Quote\Item */
        $quoteItem = reset($arguments);

        if ($attributes = $quoteItem->getProduct()->getCustomOption('bundle_selection_attributes')) {
            $productOptions = $orderItem->getProductOptions();
            $productOptions['bundle_selection_attributes'] = $attributes->getValue();
            $orderItem->setProductOptions($productOptions);
        }
        return $orderItem;
    }
}
