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
     * @param \Magento\Sales\Model\Convert\Quote $subject
     * @param callable $proceed
     * @param \Magento\Sales\Model\Quote\Item\AbstractItem $item
     *
     * @return \Magento\Sales\Model\Order\Item|mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundItemToOrderItem(
        \Magento\Sales\Model\Convert\Quote $subject,
        \Closure $proceed,
        \Magento\Sales\Model\Quote\Item\AbstractItem $item
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item);
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
