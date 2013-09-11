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
namespace Magento\MultipleWishlist\Model;

class Search
{
    /**
     * Retrieve wishlist search results by search strategy
     *
     * @param \Magento\MultipleWishlist\Model\Search\Strategy\StrategyInterface $strategy
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function getResults(\Magento\MultipleWishlist\Model\Search\Strategy\StrategyInterface $strategy)
    {
        /* @var \Magento\Wishlist\Model\Resource\Wishlist\Collection $collection */
        $collection = \Mage::getModel('Magento\Wishlist\Model\Wishlist')->getCollection();
        $collection->addFieldToFilter('visibility', array('eq' => 1));
        $strategy->filterCollection($collection);
        return $collection;
    }
}
