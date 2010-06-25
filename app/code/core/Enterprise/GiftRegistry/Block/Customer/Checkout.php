<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Customer gift registry checkout abstract block
 */
class Enterprise_GiftRegistry_Block_Customer_Checkout extends Mage_Core_Block_Template
{
    /**
     * Get current checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function getEnabled()
    {
        return  Mage::helper('enterprise_giftregistry')->isEnabled();
    }

    /**
     * Get customer quote gift registry items
     *
     * @return array
     */
    public function getGiftRegistryQuoteItems()
    {
        $items = array();
        if ($this->_getCheckoutSession()->getQuoteId()) {
            $quote = $this->_getCheckoutSession()->getQuote();
            $model = Mage::getModel('enterprise_giftregistry/entity');
            foreach ($quote->getItemsCollection() as $quoteItem) {
                if ($registryItemId = $quoteItem->getGiftregistryItemId()) {
                    $model->loadByEntityItem($registryItemId);
                    if ($model->getShippingAddress()) {
                        $items[$quoteItem->getId()]['entity_id'] = $model->getId();
                        $items[$quoteItem->getId()]['item_id'] = $registryItemId;
                    }
                }
            }
        }
        return $items;
    }

    /**
     * Get customer quote unique gift registry item
     *
     * @return false|int
     */
    public function getItem()
    {
        $items = array();
        foreach ($this->getGiftRegistryQuoteItems() as $registryItem) {
            $items[$registryItem['entity_id']] = $registryItem['item_id'];
        }
        if (count($items) == 1) {
            return array_shift($items);
        }
        return false;
    }

    /**
     * Get select shipping address id prefix
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getAddressIdPrefix()
    {
        return Mage::helper('enterprise_giftregistry')->getAddressIdPrefix();
    }
}
