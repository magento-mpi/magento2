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
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_MultipleWishlist_Model_Search
{
    /**
     * Wishlist collection factory
     *
     * @var Magento_Wishlist_Model_Resource_Wishlist_CollectionFactory
     */
    protected $_wishlistCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Wishlist_Model_Resource_Wishlist_CollectionFactory $wishlistCollectionFactory
     */
    public function __construct(
        Magento_Wishlist_Model_Resource_Wishlist_CollectionFactory $wishlistCollectionFactory
    ) {
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
    }

    /**
     * Retrieve wishlist search results by search strategy
     *
     * @param Magento_MultipleWishlist_Model_Search_Strategy_Interface $strategy
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function getResults(Magento_MultipleWishlist_Model_Search_Strategy_Interface $strategy)
    {
        /* @var Magento_Wishlist_Model_Resource_Wishlist_Collection $collection */
        $collection = $this->_wishlistCollectionFactory->create();
        $collection->addFieldToFilter('visibility', array('eq' => 1));
        $strategy->filterCollection($collection);
        return $collection;
    }
}
