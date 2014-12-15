<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Wishlist search strategy interface
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Model\Search\Strategy;

interface StrategyInterface
{
    /**
     * Filter given wishlist collection
     *
     * @abstract
     * @param \Magento\Wishlist\Model\Resource\Wishlist\Collection $collection
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function filterCollection(\Magento\Wishlist\Model\Resource\Wishlist\Collection $collection);
}
