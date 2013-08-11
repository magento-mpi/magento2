<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist Item Configure block
 * Serves for configuring item on product view page
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @module     Wishlist
 */
class Mage_Wishlist_Block_Item_Configure extends Magento_Core_Block_Template
{
    /**
     * Returns product being edited
     *
     * @return Magento_Catalog_Model_Product
     */
    protected function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Returns wishlist item being configured
     *
     * @return Magento_Catalog_Model_Product|Mage_Wishlist_Model_Item
     */
    protected function getWishlistItem()
    {
        return Mage::registry('wishlist_item');
    }

    /**
     * Configure product view blocks
     *
     * @return Mage_Wishlist_Block_Item_Configure
     */
    protected function _prepareLayout()
    {
        // Set custom add to cart url
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
            $url = Mage::helper('Mage_Wishlist_Helper_Data')->getAddToCartUrl($this->getWishlistItem());
            $block->setCustomAddToCartUrl($url);
        }

        return parent::_prepareLayout();
    }
}
