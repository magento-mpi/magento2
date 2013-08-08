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
 * Wishlist search module
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_MultipleWishlist_Model_Search
{
    /**
     * Retrieve wishlist search results by search strategy
     *
     * @param Magento_MultipleWishlist_Model_Search_Strategy_Interface $strategy
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function getResults(Magento_MultipleWishlist_Model_Search_Strategy_Interface $strategy)
    {
        /* @var Magento_Wishlist_Model_Resource_Wishlist_Collection $collection */
        $collection = Mage::getModel('Magento_Wishlist_Model_Wishlist')->getCollection();
        $collection->addFieldToFilter('visibility', array('eq' => 1));
        $strategy->filterCollection($collection);
        return $collection;
    }
}
