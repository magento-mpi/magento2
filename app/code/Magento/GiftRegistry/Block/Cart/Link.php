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
 * Cart link block
 */
class Magento_GiftRegistry_Block_Cart_Link extends Magento_Core_Block_Template
{

    /**
     * Return add url
     *
     * @return bool
     */
    public function getAddUrl()
    {
        return $this->getUrl('giftregistry/index/cart');
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
     * Return list of current customer gift registries
     *
     * @return Magento_GiftRegistry_Model_Resource_GiftRegistry_Collection
     */
    public function getEntityValues()
    {
        return Mage::helper('Magento_GiftRegistry_Helper_Data')->getCurrentCustomerEntityOptions();
    }
}
