<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

use Magento\MultipleWishlist\Test\Page\SearchResult;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

/**
 * Class AssertWishlistIsPrivate
 * Assert that Wishlist can't be find by another Customer (or guest) via "Wishlist Search".
 */
class AssertWishlistIsPrivate extends AssertWishlistIsPublic
{
    /**
     * Assert wish list is private
     *
     * @param SearchResult $searchResult
     * @param MultipleWishlist $multipleWishlist
     * @return void
     */
    protected function assert(SearchResult $searchResult, MultipleWishlist $multipleWishlist)
    {
        \PHPUnit_Framework_Assert::assertFalse(
            $searchResult->getWishlistSearchResultBlock()->visibleInGrid($multipleWishlist->getName()),
            'Multiple wish list is not private.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Multiple wish list is private.';
    }
}
