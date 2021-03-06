<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\MultipleWishlist\Test\Constraint;

use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Abstract Class AssertMultipleWishlistSuccessSaveMessage
 * Assert success message is displayed
 */
abstract class AbstractAssertMultipleWishlistSuccessMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success message
     *
     * @var string
     */
    protected $message;

    /**
     * Assert success message is displayed
     *
     * @param WishlistIndex $wishlistIndex
     * @param MultipleWishlist $multipleWishlist
     * @return void
     */
    public function processAssert(WishlistIndex $wishlistIndex, MultipleWishlist $multipleWishlist)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf($this->message, $multipleWishlist->getName()),
            $wishlistIndex->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
        );
    }
}
