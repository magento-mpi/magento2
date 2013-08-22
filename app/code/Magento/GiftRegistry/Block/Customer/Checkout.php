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
     * Get current checkout session
     *
     * @return Magento_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session');
    }

    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function getEnabled()
    {
        return  Mage::helper('Magento_GiftRegistry_Helper_Data')->isEnabled();
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
            $model = Mage::getModel('Magento_GiftRegistry_Model_Entity');
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
        return Mage::helper('Magento_GiftRegistry_Helper_Data')->getAddressIdPrefix();
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
        $quoteAddressItems = Mage::getSingleton('Magento_Checkout_Model_Type_Multishipping')->getQuoteShippingAddressesItems();
        foreach ($quoteAddressItems as $index => $quoteAddressItem) {
            $quoteItemId = $quoteAddressItem->getQuoteItem()->getId();
            if (!$quoteAddressItem->getCustomerAddressId() && in_array($quoteItemId, $registryQuoteItemIds)) {
                $result[] = $index;
            }
        }
        return $result;
    }
}
