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
namespace Magento\Wishlist\Block\Item;

class Configure extends \Magento\Core\Block\Template
{
    /**
     * Returns product being edited
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function getProduct()
    {
        return \Mage::registry('product');
    }

    /**
     * Returns wishlist item being configured
     *
     * @return \Magento\Catalog\Model\Product|\Magento\Wishlist\Model\Item
     */
    protected function getWishlistItem()
    {
        return \Mage::registry('wishlist_item');
    }

    /**
     * Configure product view blocks
     *
     * @return \Magento\Wishlist\Block\Item\Configure
     */
    protected function _prepareLayout()
    {
        // Set custom add to cart url
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
            $url = \Mage::helper('Magento\Wishlist\Helper\Data')->getAddToCartUrl($this->getWishlistItem());
            $block->setCustomAddToCartUrl($url);
        }

        return parent::_prepareLayout();
    }
}
