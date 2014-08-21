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
 * Class AssertMultipleWishlistIsPublic
 * Assert wish list is public
 */
class AssertMultipleWishlistIsPublic extends AbstractAssertMultipleWishlistState
{
    /**
     * Assert that Wishlist can be find by another Customer (or guest) via "Wishlist Search"
     *
     * @param SearchResult $searchResult
     * @param MultipleWishlist $multipleWishlist
     * @return void
     */
    protected function assert(SearchResult $searchResult, MultipleWishlist $multipleWishlist)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $searchResult->getWishlistSearchResultBlock()->isWishlistVisibleInGrid($multipleWishlist->getName()),
            'Multiple wish list is not public.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Multiple wish list is public.';
    }
}
