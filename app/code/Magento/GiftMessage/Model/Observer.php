<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftMessage\Model;

/**
 * Gift Message Observer Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Observer extends \Magento\Framework\Object
{
    /**
     * Gift message message
     *
     * @var \Magento\GiftMessage\Helper\Message|null
     */
    protected $_giftMessageMessage = null;

    /**
     * @var \Magento\GiftMessage\Model\MessageFactory
     */
    protected $_messageFactory;

    /**
     * @param \Magento\GiftMessage\Model\MessageFactory $messageFactory
     * @param \Magento\GiftMessage\Helper\Message $giftMessageMessage
     */
    public function __construct(
        \Magento\GiftMessage\Model\MessageFactory $messageFactory,
        \Magento\GiftMessage\Helper\Message $giftMessageMessage
    ) {
        $this->_messageFactory = $messageFactory;
        $this->_giftMessageMessage = $giftMessageMessage;
    }

    /**
     * Set gift messages to order from quote address
     *
     * @param \Magento\Framework\Object $observer
     * @return $this
     */
    public function salesEventConvertQuoteAddressToOrder($observer)
    {
        if ($observer->getEvent()->getAddress()->getGiftMessageId()) {
            $observer->getEvent()->getOrder()->setGiftMessageId(
                $observer->getEvent()->getAddress()->getGiftMessageId()
            );
        }
        return $this;
    }

    /**
     * Set gift messages to order from quote address
     *
     * @param \Magento\Framework\Object $observer
     * @return $this
     */
    public function salesEventConvertQuoteToOrder($observer)
    {
        $observer->getEvent()->getOrder()->setGiftMessageId($observer->getEvent()->getQuote()->getGiftMessageId());
        return $this;
    }

    /**
     * Operate with gift messages on checkout proccess
     *
     * @param \Magento\Framework\Object $observer
     * @return $this
     */
    public function checkoutEventCreateGiftMessage($observer)
    {
        $giftMessages = $observer->getEvent()->getRequest()->getParam('giftmessage');
        $quote = $observer->getEvent()->getQuote();
        /* @var $quote \Magento\Sales\Model\Quote */
        if (!is_array($giftMessages)) {
            return $this;
        }
        // types are 'quote', 'quote_item', etc
        foreach ($giftMessages as $type => $giftmessageEntities) {
            foreach ($giftmessageEntities as $entityId => $message) {
                $giftMessage = $this->_messageFactory->create();
                switch ($type) {
                    case 'quote':
                        $entity = $quote;
                        break;
                    case 'quote_item':
                        $entity = $quote->getItemById($entityId);
                        break;
                    case 'quote_address':
                        $entity = $quote->getAddressById($entityId);
                        break;
                    case 'quote_address_item':
                        $entity = $quote->getAddressById($message['address'])->getItemById($entityId);
                        break;
                    default:
                        $entity = $quote;
                        break;
                }

                if ($entity->getGiftMessageId()) {
                    $giftMessage->load($entity->getGiftMessageId());
                }

                if (trim($message['message']) == '') {
                    if ($giftMessage->getId()) {
                        try {
                            $giftMessage->delete();
                            $entity->setGiftMessageId(0)->save();
                        } catch (\Exception $e) {
                        }
                    }
                    continue;
                }

                try {
                    $giftMessage->setSender(
                        $message['from']
                    )->setRecipient(
                        $message['to']
                    )->setMessage(
                        $message['message']
                    )->save();

                    $entity->setGiftMessageId($giftMessage->getId())->save();
                } catch (\Exception $e) {
                }
            }
        }
        return $this;
    }

    /**
     * Duplicates giftmessage from order to quote on import or reorder
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function salesEventOrderToQuote($observer)
    {
        $order = $observer->getEvent()->getOrder();
        // Do not import giftmessage data if order is reordered
        if ($order->getReordered()) {
            return $this;
        }

        if (!$this->_giftMessageMessage->isMessagesAvailable('order', $order, $order->getStore())) {
            return $this;
        }
        $giftMessageId = $order->getGiftMessageId();
        if ($giftMessageId) {
            $giftMessage = $this->_messageFactory->create()->load($giftMessageId)->setId(null)->save();
            $observer->getEvent()->getQuote()->setGiftMessageId($giftMessage->getId());
        }

        return $this;
    }

    /**
     * Duplicates giftmessage from order item to quote item on import or reorder
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function salesEventOrderItemToQuoteItem($observer)
    {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $observer->getEvent()->getOrderItem();
        // Do not import giftmessage data if order is reordered
        $order = $orderItem->getOrder();
        if ($order && $order->getReordered()) {
            return $this;
        }

        $isAvailable = $this->_giftMessageMessage->isMessagesAvailable(
            'order_item',
            $orderItem,
            $orderItem->getStoreId()
        );
        if (!$isAvailable) {
            return $this;
        }

        /** @var $quoteItem \Magento\Sales\Model\Quote\Item */
        $quoteItem = $observer->getEvent()->getQuoteItem();
        if ($giftMessageId = $orderItem->getGiftMessageId()) {
            $giftMessage = $this->_messageFactory->create()->load($giftMessageId)->setId(null)->save();
            $quoteItem->setGiftMessageId($giftMessage->getId());
        }
        return $this;
    }
}
