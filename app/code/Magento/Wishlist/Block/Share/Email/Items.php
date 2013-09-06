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
 * Wishlist block customer items
 *
 * @category   Magento
 * @package    Magento_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Wishlist_Block_Share_Email_Items extends Magento_Wishlist_Block_Abstract
{
    protected $_template = 'email/items.phtml';

    /**
     * Retrieve Product View URL
     *
     * @param Magento_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getProductUrl($product, $additional = array())
    {
        $additional['_store_to_url'] = true;
        return parent::getProductUrl($product, $additional);
    }

    /**
     * Retrieve URL for add product to shopping cart
     *
     * @param Magento_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array())
    {
        $additional['nocookie'] = 1;
        $additional['_store_to_url'] = true;
        return parent::getAddToCartUrl($product, $additional);
    }

    /**
     * Check whether whishlist item has description
     *
     * @param Magento_Wishlist_Model_Item $item
     * @return bool
     */
    public function hasDescription($item)
    {
        $hasDescription = parent::hasDescription($item);
        if ($hasDescription) {
            return ($item->getDescription() !== Mage::helper('Magento_Wishlist_Helper_Data')->defaultCommentString());
        }
        return $hasDescription;
    }
}
