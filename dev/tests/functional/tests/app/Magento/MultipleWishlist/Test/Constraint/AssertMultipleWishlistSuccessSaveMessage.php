<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\MultipleWishlist\Test\Constraint;

/**
 * Class AssertMultipleWishlistSuccessSaveMessage
 * Assert success save message is displayed
 */
class AssertMultipleWishlistSuccessSaveMessage extends AbstractAssertMultipleWishlistSuccessMessage
{
    /**
     * Success message
     *
     * @var string
     */
    protected $message = 'Wish List "%s" was saved.';

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Multiple wish list success save message is present.';
    }
}
