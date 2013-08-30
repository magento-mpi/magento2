<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist item selector in wishlist table
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_MultipleWishlist_Block_Customer_Wishlist_Item_Column_Selector_Cart
    extends Magento_MultipleWishlist_Block_Customer_Wishlist_Item_Column_Selector
{
    /**
     * Retrieve block html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getItem()->getProduct()->isSaleable()) {
            return parent::_toHtml();
        }
        return '';
    }
}
