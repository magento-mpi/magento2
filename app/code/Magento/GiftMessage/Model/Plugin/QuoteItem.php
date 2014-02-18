<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftMessage\Model\Plugin;

class QuoteItem
{
    /**
     * @var \Magento\GiftMessage\Helper\Message
     */
    protected $_helper;

    /**
     * @param \Magento\GiftMessage\Helper\Message $helper
     */
    public function __construct(
        \Magento\GiftMessage\Helper\Message $helper
    ) {
        $this->_helper = $helper;
    }

    /**
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
