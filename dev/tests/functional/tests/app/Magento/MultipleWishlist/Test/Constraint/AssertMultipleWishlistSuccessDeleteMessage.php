<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

/**
 * Class AssertMultipleWishlistSuccessDeleteMessage
 * Assert delete message is displayed
 */
class AssertMultipleWishlistSuccessDeleteMessage extends AbstractAssertMultipleWishlistSuccessMessage
{
    /**
     * Success message
     *
     * @var string
     */
    protected $message = 'Wish list "%s" has been deleted.';

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Multiple wish list delete message is present.';
    }
}
