<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Plugin;

class QuoteItem
{
    /**
     * Copy gift registry item id flag from quote item to order item
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

        if ($quoteItem instanceof \Magento\Sales\Model\Quote\Address\Item) {
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
