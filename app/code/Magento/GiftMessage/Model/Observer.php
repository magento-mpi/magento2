<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift Message Observer Model
 *
 * @category   Magento
 * @package    Magento_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftMessage_Model_Observer extends Magento_Object
{
    /**
     * Gift message message
     *
     * @var Magento_GiftMessage_Helper_Message
     */
    protected $_giftMessageMessage = null;

    /**
     * @param Magento_GiftMessage_Helper_Message $giftMessageMessage
     */
    public function __construct(
        Magento_GiftMessage_Helper_Message $giftMessageMessage
    ) {
        $this->_giftMessageMessage = $giftMessageMessage;
    }

    /**
     * Set gift messages to order item on import item
     *
     * @param Magento_Object $observer
     * @return Magento_GiftMessage_Model_Observer
     */
    public function salesEventConvertQuoteItemToOrderItem($observer)
    {
        $orderItem = $observer->getEvent()->getOrderItem();
        $quoteItem = $observer->getEvent()->getItem();

        $isAvailable = $this->_giftMessageMessage
            ->getIsMessagesAvailable('item', $quoteItem, $quoteItem->getStoreId());

        $orderItem->setGiftMessageId($quoteItem->getGiftMessageId())
            ->setGiftMessageAvailable($isAvailable);
        return $this;
    }

    /**
     * Set gift messages to order from quote address
     *
     * @param Magento_Object $observer
     * @return Magento_GiftMessage_Model_Observer
     */
    public function salesEventConvertQuoteAddressToOrder($observer)
    {
        if ($observer->getEvent()->getAddress()->getGiftMessageId()) {
            $observer->getEvent()->getOrder()
                ->setGiftMessageId($observer->getEvent()->getAddress()->getGiftMessageId());
        }
        return $this;
    }

    /**
     * Set gift messages to order from quote address
     *
     * @param Magento_Object $observer
     * @return Magento_GiftMessage_Model_Observer
     */
    public function salesEventConvertQuoteToOrder($observer)
    {
        $observer->getEvent()->getOrder()
            ->setGiftMessageId($observer->getEvent()->getQuote()->getGiftMessageId());
        return $this;
    }

    /**
     * Operate with gift messages on checkout proccess
     *
     * @param Varieb_Object $observer
     * @return Magento_GiftMessage_Model_Observer
     */
    public function checkoutEventCreateGiftMessage($observer)
    {
        $giftMessages = $observer->getEvent()->getRequest()->getParam('giftmessage');
        $quote = $observer->getEvent()->getQuote();
        /* @var $quote Magento_Sales_Model_Quote */
        if (is_array($giftMessages)) {
            foreach ($giftMessages as $entityId=>$message) {

                $giftMessage = Mage::getModel('Magento_GiftMessage_Model_Message');

                switch ($message['type']) {
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

                if (trim($message['message'])=='') {
                    if ($giftMessage->getId()) {
                        try{
                            $giftMessage->delete();
                            $entity->setGiftMessageId(0)
                                ->save();
                        }
                        catch (Exception $e) { }
                    }
                    continue;
                }

                try {
                    $giftMessage->setSender($message['from'])
                        ->setRecipient($message['to'])
                        ->setMessage($message['message'])
                        ->save();

                    $entity->setGiftMessageId($giftMessage->getId())
                        ->save();

                }
                catch (Exception $e) { }
            }
        }
        return $this;
    }

    /**
     * Duplicates giftmessage from order to quote on import or reorder
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftMessage_Model_Observer
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
            $giftMessage = Mage::getModel('Magento_GiftMessage_Model_Message')->load($giftMessageId)
                ->setId(null)
                ->save();
            $observer->getEvent()->getQuote()->setGiftMessageId($giftMessage->getId());
        }

        return $this;
    }

    /**
     * Duplicates giftmessage from order item to quote item on import or reorder
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftMessage_Model_Observer
     */
    public function salesEventOrderItemToQuoteItem($observer)
    {
        /** @var $orderItem Magento_Sales_Model_Order_Item */
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

        /** @var $quoteItem Magento_Sales_Model_Quote_Item */
        $quoteItem = $observer->getEvent()->getQuoteItem();
        if ($giftMessageId = $orderItem->getGiftMessageId()) {
            $giftMessage = Mage::getModel('Magento_GiftMessage_Model_Message')->load($giftMessageId)
                ->setId(null)
                ->save();
            $quoteItem->setGiftMessageId($giftMessage->getId());
        }
        return $this;
    }
}
