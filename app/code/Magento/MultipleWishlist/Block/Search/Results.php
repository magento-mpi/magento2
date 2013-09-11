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
namespace Magento\MultipleWishlist\Block\Search;

class Results extends \Magento\Core\Block\Template
{
    /**
     * Retrieve wishlist search results
     *
     * @return Magento_Wishlist_Model_Resource_Collection
     */
    public function getSearchResults()
    {
        return \Mage::registry('search_results');
    }

    /**
     * Return frontend registry link
     *
     * @param \Magento\Wishlist\Model\Wishlist $item
     * @return string
     */
    public function getWishlistLink(\Magento\Wishlist\Model\Wishlist $item)
    {
        return $this->getUrl('*/search/view', array('wishlist_id' => $item->getId()));
    }
}
