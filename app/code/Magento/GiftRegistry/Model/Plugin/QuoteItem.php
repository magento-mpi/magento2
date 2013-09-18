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
