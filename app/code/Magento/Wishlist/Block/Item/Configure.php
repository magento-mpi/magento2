<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist Item Configure block
 * Serves for configuring item on product view page
 *
 * @category   Magento
 * @package    Magento_Wishlist
 * @module     Wishlist
 */
class Magento_Wishlist_Block_Item_Configure extends Magento_Core_Block_Template
{
    /**
     * Wishlist data
     *
     * @var Magento_Wishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        parent::__construct($coreData, $context, $data);
    }

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
     * @return Magento_Catalog_Model_Product|Magento_Wishlist_Model_Item
     */
    protected function getWishlistItem()
    {
        return Mage::registry('wishlist_item');
    }

    /**
     * Configure product view blocks
     *
     * @return Magento_Wishlist_Block_Item_Configure
     */
    protected function _prepareLayout()
    {
        // Set custom add to cart url
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
            $url = $this->_wishlistData->getAddToCartUrl($this->getWishlistItem());
            $block->setCustomAddToCartUrl($url);
        }

        return parent::_prepareLayout();
    }
}
