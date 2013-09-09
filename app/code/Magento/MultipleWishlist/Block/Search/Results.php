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
 * Multiple wishlist search results
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_MultipleWishlist_Block_Search_Results extends Magento_Core_Block_Template
{
    /**
     * Retrieve wishlist search results
     *
     * @return Magento_Wishlist_Model_Resource_Collection
     */
    public function getSearchResults()
    {
        return Mage::registry('search_results');
    }

    /**
     * Return frontend registry link
     *
     * @param Magento_Wishlist_Model_Wishlist $item
     * @return string
     */
    public function getWishlistLink(Magento_Wishlist_Model_Wishlist $item)
    {
        return $this->getUrl('*/search/view', array('wishlist_id' => $item->getId()));
    }
}
