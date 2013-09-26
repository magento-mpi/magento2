<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer gift registry checkout abstract block
 */
class Magento_GiftRegistry_Block_Customer_Checkout extends Magento_Core_Block_Template
{
    /**
     * @var Magento_GiftRegistry_Model_EntityFactory
     */
    protected $entityFactory;

    /**
     * Gift registry data
     *
     * @var Magento_GiftRegistry_Helper_Data
     */
    protected $_giftRegistryData = null;

    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $customerSession;

    /**
     * @var Magento_Checkout_Model_Type_Multishipping
     */
    protected $typeMultiShipping;

    /**
     * @param Magento_GiftRegistry_Helper_Data $giftRegistryData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Checkout_Model_Session $customerSession
     * @param Magento_Checkout_Model_Type_Multishipping $typeMultiShipping
     * @param Magento_GiftRegistry_Model_EntityFactory $entityFactory
     * @param array $data
     */
    public function __construct(
        Magento_GiftRegistry_Helper_Data $giftRegistryData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Checkout_Model_Session $customerSession,
        Magento_Checkout_Model_Type_Multishipping $typeMultiShipping,
        Magento_GiftRegistry_Model_EntityFactory $entityFactory,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        $this->customerSession = $customerSession;
        $this->typeMultiShipping = $typeMultiShipping;
        $this->entityFactory = $entityFactory;

        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get current checkout session
     *
     * @return Magento_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return $this->customerSession;
    }

    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function getEnabled()
    {
        return  $this->_giftRegistryData->isEnabled();
    }

    /**
     * Get customer quote gift registry items
     *
     * @return array
     */
    protected function _getGiftRegistryQuoteItems()
    {
        $items = array();
        if ($this->_getCheckoutSession()->getQuoteId()) {
            $quote = $this->_getCheckoutSession()->getQuote();
            $model = $this->entityFactory->create();
            foreach ($quote->getItemsCollection() as $quoteItem) {
                $item = array();
                if ($registryItemId = $quoteItem->getGiftregistryItemId()) {
                    $model->loadByEntityItem($registryItemId);
                    $item['entity_id'] = $model->getId();
                    $item['item_id'] = $registryItemId;
                    $item['is_address'] = ($model->getShippingAddress()) ? 1 : 0;
                    $items[$quoteItem->getId()] = $item;
                }
            }
        }
        return $items;
    }

   /**
     * Get quote gift registry items for multishipping checkout
     *
     * @return array
     */
    public function getItems()
    {
        $items = array();
        foreach ($this->_getGiftRegistryQuoteItems() as $quoteItemId => $item) {
            if ($item['is_address']) {
                $items[$quoteItemId] = $item;
            }
        }
        return $items;
    }

    /**
     * Get quote unique gift registry item for onepage checkout
     *
     * @return false|int
     */
    public function getItem()
    {
        $items = array();
        foreach ($this->_getGiftRegistryQuoteItems() as $registryItem) {
            $items[$registryItem['entity_id']] = $registryItem;
        }
        if (count($items) == 1) {
            $item = array_shift($items);
            if ($item['is_address']) {
                return $item['item_id'];
            }
        }
        return false;
    }

    /**
     * Get select shipping address id prefix
     *
     * @return Magento_Checkout_Model_Session
     */
    public function getAddressIdPrefix()
    {
        return $this->_giftRegistryData->getAddressIdPrefix();
    }

    /**
     * Retrieve giftregistry selected addresses indexes
     *
     * @return array
     */
    public function getGiftregistrySelectedAddressesIndexes()
    {
        $result = array();
        $registryQuoteItemIds = array_keys($this->getItems());
        $quoteAddressItems = $this->typeMultiShipping->getQuoteShippingAddressesItems();
        foreach ($quoteAddressItems as $index => $quoteAddressItem) {
            $quoteItemId = $quoteAddressItem->getQuoteItem()->getId();
            if (!$quoteAddressItem->getCustomerAddressId() && in_array($quoteItemId, $registryQuoteItemIds)) {
                $result[] = $index;
            }
        }
        return $result;
    }
}
