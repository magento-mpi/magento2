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
class Magento_MultipleWishlist_Block_Customer_Wishlist_Item_Column_Copy
    extends Magento_MultipleWishlist_Block_Customer_Wishlist_Item_Column_Management
{
    /**
     * Checks whether column should be shown in table
     *
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Check wheter multiple wishlist functionality is enabled
     *
     * @return bool
     */
    public function isMultipleEnabled()
    {
        return Mage::helper('Magento_MultipleWishlist_Helper_Data')->isMultipleEnabled();
    }

    /**
     * Get wishlist item copy url
     *
     * @return string
     */
    public function getCopyItemUrl()
    {
        return $this->getUrl('wishlist/index/copyitem');
    }

    /**
     * Retrieve column javascript code
     *
     * @return string
     */
    public function getJs()
    {
        return parent::getJs() . "
            if (typeof Enterprise.Wishlist.url == 'undefined') {
                Enterprise.Wishlist.url = {};
            }
            Enterprise.Wishlist.url.copyItem = '" . $this->getCopyItemUrl() . "';
        ";
    }
}
