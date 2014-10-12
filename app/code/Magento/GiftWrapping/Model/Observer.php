<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model;

/**
 * Gift wrapping observer model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Observer
{
    /**
     * Gift wrapping data
     *
     * @var \Magento\GiftWrapping\Helper\Data|null
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
     * @return $this
     */
    protected function _saveItemInfo($entity, $data)
    {
        if (is_array($data) && isset($data['design'])) {
            $wrapping = $this->_wrappingFactory->create()->load($data['design']);
            $entity->setGwId($wrapping->getId())->save();
        }
        return $this;
    }

    /**
     * Prepare entire order info about gift wrapping
     *
     * @param mixed $entity
     * @param array $data
     * @return $this
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
     * @param \Magento\Framework\Object $observer
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function checkoutProcessWrappingInfo($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $giftWrappingInfo = $request->getParam('giftwrapping');

        if (!is_array($giftWrappingInfo)) {
            return $this;
        }
        $quote = $observer->getEvent()->getQuote();
        foreach ($giftWrappingInfo as $type => $wrappingEntities) {
            if (!is_array($wrappingEntities)) {
                throw new \InvalidArgumentException('Invalid entity by index ' . $type);
            }
            foreach ($wrappingEntities as $entityId => $data) {
                switch ((string)$type) {
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
                        $giftOptionsInfo = $request->getParam('giftoptions');
                        if (!is_array($giftOptionsInfo) || empty($giftOptionsInfo)) {
                            throw new \InvalidArgumentException('Invalid "giftoptions" parameter');
                        }
                        $entity = $quote->getAddressById(
                            $giftOptionsInfo[$type][$entityId]['address']
                        )->getItemById(
                            $entityId
                        );
                        $this->_saveItemInfo($entity, $data);
                        break;
                    default:
                        throw new \InvalidArgumentException('Invalid wrapping type:' . $type);
                }
            }
        }
        return $this;
    }

    /**
     * Process admin order creation
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function processOrderCreationData($observer)
    {
        $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();
        $request = $observer->getEvent()->getRequest();
        if (isset($request['giftwrapping'])) {
            foreach ($request['giftwrapping'] as $entityType => $entityData) {
                foreach ($entityData as $entityId => $data) {
                    switch ($entityType) {
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function quoteCollectTotalsBefore(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $quote->setIsNewGiftWrappingCollecting(true);
        $quote->setIsNewGiftWrappingTaxCollecting(true);
    }

    /**
     * Add gift wrapping items into payment checkout
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function addPaymentGiftWrappingItem(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Payment\Model\Cart $cart */
        $cart = $observer->getEvent()->getCart();
        $totalWrapping = 0;
        $totalCard = 0;
        $salesEntity = $cart->getSalesModel();
        foreach ($salesEntity->getAllItems() as $item) {
            $originalItem = $item->getOriginalItem();
            if (!$originalItem->getParentItem() && $originalItem->getGwId() && $originalItem->getGwBasePrice()) {
                $totalWrapping += $originalItem->getGwBasePrice();
            }
        }
        if ($salesEntity->getDataUsingMethod('gw_id') && $salesEntity->getDataUsingMethod('gw_base_price')) {
            $totalWrapping += $salesEntity->getDataUsingMethod('gw_base_price');
        }
        if ($salesEntity->getDataUsingMethod('gw_add_card') && $salesEntity->getDataUsingMethod('gw_card_base_price')
        ) {
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function prepareGiftOptionsItems(\Magento\Framework\Event\Observer $observer)
    {
        $items = $observer->getEvent()->getItems();
        foreach ($items as $item) {
            $allowed = $item->getProduct()->getGiftWrappingAvailable();
            if ($this->_giftWrappingData->isGiftWrappingAvailableForProduct($allowed) && !$item->getIsVirtual()) {
                $item->setIsGiftOptionsAvailable(true);
            }
        }
        return $this;
    }

    /**
     * Import giftwrapping data from order to quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
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
        $quote->setGwId(
            $order->getGwId()
        )->setGwAllowGiftReceipt(
            $order->getGwAllowGiftReceipt()
        )->setGwAddCard(
            $order->getGwAddCard()
        );
        return $this;
    }

    /**
     * Import giftwrapping data from order item to quote item
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function salesEventOrderItemToQuoteItem($observer)
    {
        // @var $orderItem \Magento\Sales\Model\Order\Item
        $orderItem = $observer->getEvent()->getOrderItem();
        // Do not import giftwrapping data if order is reordered or GW is not available for items
        $order = $orderItem->getOrder();
        $giftWrappingHelper = $this->_giftWrappingData;
        if ($order && ($order->getReordered() || !$giftWrappingHelper->isGiftWrappingAvailableForItems(
            $order->getStore()->getId()
        ))
        ) {
            return $this;
        }
        $quoteItem = $observer->getEvent()->getQuoteItem();
        $quoteItem->setGwId(
            $orderItem->getGwId()
        )->setGwBasePrice(
            $orderItem->getGwBasePrice()
        )->setGwPrice(
            $orderItem->getGwPrice()
        )->setGwBaseTaxAmount(
            $orderItem->getGwBaseTaxAmount()
        )->setGwTaxAmount(
            $orderItem->getGwTaxAmount()
        );
        return $this;
    }
}
