<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multiple wishlist search results
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Search_Results extends Magento_Core_Block_Template
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
