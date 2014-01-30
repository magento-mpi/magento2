<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping observer model
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Model;

class Observer
{
    /**
     * Gift wrapping data
     *
     * @var \Magento\GiftWrapping\Helper\Data
     */
    protected $_giftWrappingData = null;

    /**
     * @var \Magento\GiftWrapping\Model\WrappingFactory
     */
    protected $_wrappingFactory;

    /**
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param \Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory
     */
    public function __construct(
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        \Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        $this->_wrappingFactory = $wrappingFactory;
    }

    /**
     * Prepare quote item info about gift wrapping
     *
     * @param mixed $entity
     * @param array $data
     * @return \Magento\GiftWrapping\Model\Observer
     */
    protected function _saveItemInfo($entity, $data)
    {
        if (is_array($data) && isset($data['design'])) {
            $wrapping = $this->_wrappingFactory->create()->load($data['design']);
            $entity->setGwId($wrapping->getId())
                ->save();
        }
        return $this;
    }

    /**
     * Prepare entire order info about gift wrapping
     *
     * @param mixed $entity
     * @param array $data
     * @return \Magento\GiftWrapping\Model\Observer
     */
    protected function _saveOrderInfo($entity, $data)
    {
        if (is_array($data)) {
            $wrappingInfo = array();
            if (isset($data['design'])) {
                $wrapping = $this->_wrappingFactory->create()->load($data['design']);
                $wrappingInfo['gw_id'] = $wrapping->getId();
            }
            $wrappingInfo['gw_allow_gift_receipt'] = isset($data['allow_gift_receipt']);
            $wrappingInfo['gw_add_card'] = isset($data['add_printed_card']);
            if ($entity->getShippingAddress()) {
                $entity->getShippingAddress()->addData($wrappingInfo);
            }
            $entity->addData($wrappingInfo)->save();
        }
        return $this;
    }

    /**
     * Process gift wrapping options on checkout proccess
     *
     * @param \Magento\Object $observer
     * @return \Magento\GiftWrapping\Model\Observer
     */
    public function checkoutProcessWrappingInfo($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $giftWrappingInfo = $request->getParam('giftwrapping');

        if (is_array($giftWrappingInfo)) {
            $quote = $observer->getEvent()->getQuote();
            $giftOptionsInfo = $request->getParam('giftoptions');
            foreach ($giftWrappingInfo as $entityId => $data) {
                $info = array();
                if (!is_array($giftOptionsInfo) || empty($giftOptionsInfo[$entityId]['type'])) {
                    continue;
                }
                switch ($giftOptionsInfo[$entityId]['type']) {
                    case 'quote':
                        $entity = $quote;
                        $this->_saveOrderInfo($entity, $data);
                        break;
                    case 'quote_item':
                        $entity = $quote->getItemById($entityId);
                        $this->_saveItemInfo($entity, $data);
                        break;
                    case 'quote_address':
                        $entity = $quote->getAddressById($entityId);
                        $this->_saveOrderInfo($entity, $data);
                        break;
                    case 'quote_address_item':
                        $entity = $quote
                            ->getAddressById($giftOptionsInfo[$entityId]['address'])
                            ->getItemById($entityId);
                        $this->_saveItemInfo($entity, $data);
                        break;
                }
            }
        }
        return $this;
    }

    /**
     * Process admin order creation
     *
     * @param \Magento\Event\Observer $observer
     */
    public function processOrderCreationData($observer)
    {
        $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();
        $request = $observer->getEvent()->getRequest();
        if (isset($request['giftwrapping'])) {
            $info = array();
            foreach ($request['giftwrapping'] as $entityId => $data) {
                if (isset($data['type'])) {
                    switch ($data['type']) {
                        case 'quote':
                            $entity = $quote;
                            $this->_saveOrderInfo($entity, $data);
                            break;
                        case 'quote_item':
                            $entity = $quote->getItemById($entityId);
                            $this->_saveItemInfo($entity, $data);
                            break;
                    }
                }
            }
        }
    }

    /**
     * Set the flag is it new collecting totals
     *
     * @param \Magento\Event\Observer $observer
     */
    public function quoteCollectTotalsBefore(\Magento\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $quote->setIsNewGiftWrappingCollecting(true);
        $quote->setIsNewGiftWrappingTaxCollecting(true);
    }

    /**
     * Add gift wrapping items into payment checkout
     *
     * @param \Magento\Event\Observer $observer
     */
    public function addPaymentGiftWrappingItem(\Magento\Event\Observer $observer)
    {
        /** @var \Magento\Payment\Model\Cart $cart */
        $cart = $observer->getEvent()->getCart();
        if (!$cart) {
            return;
        }
        $totalWrapping = 0;
        $totalCard = 0;
        $salesEntity = $cart->getSalesModel();
        foreach ($salesEntity->getOriginalModel()->getAllItems() as $item) {
            if (!$item->getParentItem() && $item->getGwId() && $item->getGwBasePrice()) {
                $totalWrapping += $item->getGwBasePrice();
            }
        }
        if ($salesEntity->getDataUsingMethod('gw_id') && $salesEntity->getDataUsingMethod('gw_base_price')) {
            $totalWrapping += $salesEntity->getDataUsingMethod('gw_base_price');
        }
        if ($salesEntity->getDataUsingMethod('gw_add_card') && $salesEntity->getDataUsingMethod('gw_card_base_price')) {
            $totalCard += $salesEntity->getDataUsingMethod('gw_card_base_price');
        }
        if ($totalWrapping) {
            $cart->addCustomItem(__('Gift Wrapping'), 1, $totalWrapping);
        }
        if ($totalCard) {
            $cart->addCustomItem(__('Printed Card'), 1, $totalCard);
        }
    }

    /**
     * Set gift options available flag for items
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GiftWrapping\Model\Observer
     */
    public function prepareGiftOptpionsItems(\Magento\Event\Observer $observer)
    {
       $items = $observer->getEvent()->getItems();
       foreach ($items as $item) {
           $allowed = $item->getProduct()->getGiftWrappingAvailable();
           if ($this->_giftWrappingData->isGiftWrappingAvailableForProduct($allowed)
               && !$item->getIsVirtual()) {
               $item->setIsGiftOptionsAvailable(true);
           }
       }
       return $this;
    }

    /**
     * Clear gift wrapping and printed card if customer uses GoogleCheckout payment method
     *
     * @param \Magento\Event\Observer $observer
     */
    public function googlecheckoutCheckoutBefore(\Magento\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        foreach ($quote->getAllItems() as $item) {
            $item->setGwId(false);
        }
        $quote->setGwAddCard(false);
        $quote->setGwId(false);
    }

    /**
     * Import giftwrapping data from order to quote
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GiftWrapping\Model\Observer
     */
    public function salesEventOrderToQuote($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $storeId = $order->getStore()->getId();
        // Do not import giftwrapping data if order is reordered or GW is not available for order
        $giftWrappingHelper = $this->_giftWrappingData;
        if ($order->getReordered() || !$giftWrappingHelper->isGiftWrappingAvailableForOrder($storeId)) {
            return $this;
        }
        $quote = $observer->getEvent()->getQuote();
        $quote->setGwId($order->getGwId())
            ->setGwAllowGiftReceipt($order->getGwAllowGiftReceipt())
            ->setGwAddCard($order->getGwAddCard());
        return $this;
    }

    /**
     * Import giftwrapping data from order item to quote item
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GiftWrapping\Model\Observer
     */
    public function salesEventOrderItemToQuoteItem($observer)
    {
        // @var $orderItem \Magento\Sales\Model\Order\Item
        $orderItem = $observer->getEvent()->getOrderItem();
        // Do not import giftwrapping data if order is reordered or GW is not available for items
        $order = $orderItem->getOrder();
        $giftWrappingHelper = $this->_giftWrappingData;
        if ($order && ($order->getReordered()
            || !$giftWrappingHelper->isGiftWrappingAvailableForItems($order->getStore()->getId()))
        ) {
            return $this;
        }
        $quoteItem = $observer->getEvent()->getQuoteItem();
        $quoteItem->setGwId($orderItem->getGwId())
            ->setGwBasePrice($orderItem->getGwBasePrice())
            ->setGwPrice($orderItem->getGwPrice())
            ->setGwBaseTaxAmount($orderItem->getGwBaseTaxAmount())
            ->setGwTaxAmount($orderItem->getGwTaxAmount());
        return $this;
    }
}
