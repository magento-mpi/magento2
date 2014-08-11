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
use Magento\MultipleWishlist\Test\Page\MultipleWishlistIndex;

/**
 * Class AssertWishlistSavedSuccessMessage
 * Assert success save message is displayed
 */
class AssertWishlistSavedSuccessMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const SUCCESS_MESSAGE = 'Wish List "%s" was saved.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert success save message is displayed
     *
     * @param MultipleWishlistIndex $multipleWishlistIndex
     * @param MultipleWishlist $multipleWishlist
     * @return void
     */
    public function processAssert(MultipleWishlistIndex $multipleWishlistIndex, MultipleWishlist $multipleWishlist)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_MESSAGE, $multipleWishlist->getName()),
            $multipleWishlistIndex->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Multiple wish list success save message is present.';
    }
}
