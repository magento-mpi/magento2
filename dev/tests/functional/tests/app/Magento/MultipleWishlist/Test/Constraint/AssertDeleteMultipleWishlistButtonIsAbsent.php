<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\MultipleWishlist\Test\Constraint;

use Magento\Wishlist\Test\Page\WishlistIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertDeleteMultipleWishlistButtonIsAbsent
 * Assert that there is no "Delete Wishlist" button for Customer
 */
class AssertDeleteMultipleWishlistButtonIsAbsent extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'Medium';
    /* end tags */

    /**
     * Assert that there is no "Delete Wishlist" button for Customer
     *
     * @param WishlistIndex $wishlistIndex
     * @return void
     */
    public function processAssert(WishlistIndex $wishlistIndex)
    {
        $wishlistIndex->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $wishlistIndex->getManagementBlock()->isRemoveButtonVisible(),
            '"Delete Wishlist" button is visible for Customer.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return '"Delete Wishlist" button is not visible for Customer.';
    }
}
