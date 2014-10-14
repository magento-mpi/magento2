<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\Constraint;

/**
 * Class AssertAddProductToWishlistSuccessMessage
 * Assert that success message appears on Checkout Cart page after moving product to wishlist.
 */
class AssertMoveProductToWishlistSuccessMessage extends AssertAddProductToWishlistSuccessMessage
{
    /**
     * Success add message
     */
    const SUCCESS_MESSAGE = "%s has been moved to wish list Wish List";

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message appears on Checkout Cart page after moving product to wishlist.';
    }
}
