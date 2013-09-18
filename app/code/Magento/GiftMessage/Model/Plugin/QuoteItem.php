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
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Sales\Model\Order\Item|mixed
     */
    public function aroundItemToOrderItem(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
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
