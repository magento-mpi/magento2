<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\Wishlist\Test\Page\WishlistIndex;

/**
 * Abstract Class AssertMultipleWishlistSuccessSaveMessage
 * Assert success message is displayed
 */
abstract class AbstractAssertMultipleWishlistSuccessMessage extends AbstractConstraint
{
    /**
     * Success message
     *
     * @var string
     */
    protected $message;

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

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
