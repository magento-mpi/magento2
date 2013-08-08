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
 * Wishlist search strategy interface
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Magento_MultipleWishlist_Model_Search_Strategy_Interface
{
    /**
     * Filter given wishlist collection
     *
     * @abstract
     * @param Magento_Wishlist_Model_Resource_Wishlist_Collection $collection
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function filterCollection(Magento_Wishlist_Model_Resource_Wishlist_Collection $collection);
}
