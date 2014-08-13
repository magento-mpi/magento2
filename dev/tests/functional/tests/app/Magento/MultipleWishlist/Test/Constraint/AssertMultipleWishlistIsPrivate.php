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
 * Class AssertMultipleWishlistIsPrivate
 * Assert that Wishlist can't be find by another Customer (or guest) via "Wishlist Search"
 */
class AssertMultipleWishlistIsPrivate extends AbstractAssertMultipleWishlistState
{
    /**
     * Successful message
     *
     * @var string
     */
    protected $successfulMessage = 'Multiple wish list is private.';

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
            $searchResult->getWishlistSearchResultBlock()->isWishlistVisibleInGrid($multipleWishlist->getName()),
            'Multiple wish list is not private.'
        );
    }
}
