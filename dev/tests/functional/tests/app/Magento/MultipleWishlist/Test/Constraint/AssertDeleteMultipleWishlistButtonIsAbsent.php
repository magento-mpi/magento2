<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Wishlist\Test\Page\WishlistIndex;

/**
 * Class AssertDeleteMultipleWishlistButtonIsAbsent
 * Assert that there is no "Delete Wishlist" button for Customer
 */
class AssertDeleteMultipleWishlistButtonIsAbsent extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'Medium';

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
