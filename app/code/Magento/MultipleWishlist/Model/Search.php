<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\MultipleWishlist\Model;

/**
 * Wishlist search module
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Search
{
    /**
     * Wishlist collection factory
     *
     * @var \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory
     */
    protected $_wishlistCollectionFactory;

    /**
     * Construct
     *
     * @param \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistCollectionFactory
     */
    public function __construct(\Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistCollectionFactory)
    {
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
    }

    /**
     * Retrieve wishlist search results by search strategy
     *
     * @param \Magento\MultipleWishlist\Model\Search\Strategy\StrategyInterface $strategy
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function getResults(\Magento\MultipleWishlist\Model\Search\Strategy\StrategyInterface $strategy)
    {
        /* @var \Magento\Wishlist\Model\Resource\Wishlist\Collection $collection */
        $collection = $this->_wishlistCollectionFactory->create();
        $collection->addFieldToFilter('visibility', ['eq' => 1]);
        $strategy->filterCollection($collection);
        return $collection;
    }
}
