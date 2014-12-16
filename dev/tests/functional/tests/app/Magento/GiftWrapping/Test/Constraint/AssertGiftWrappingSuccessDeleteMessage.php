<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftWrappingSuccessDeleteMessage
 * Assert that after delete Gift Wrapping successful message appears
 */
class AssertGiftWrappingSuccessDeleteMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Message displayed after delete gift wrapping
     */
    const SUCCESS_DELETE_MESSAGE = 'You deleted the gift wrapping.';

    /**
     * Assert that after delete Gift Wrapping successful message appears
     *
     * @param GiftWrappingIndex $giftWrappingIndex
     * @return void
     */
    public function processAssert(GiftWrappingIndex $giftWrappingIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $giftWrappingIndex->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift Wrapping success delete message is present.';
    }
}
