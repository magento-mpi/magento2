<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftWrappingSuccessUpdateMessage
 * Assert that after update Gift Wrapping successful message appears
 */
class AssertGiftWrappingSuccessUpdateMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Message displayed after update gift wrapping
     */
    const SUCCESS_UPDATE_MESSAGE = 'You updated a total of %d records.';

    /**
     * Assert that after update Gift Wrapping successful message appears
     *
     * @param GiftWrappingIndex $giftWrappingIndex
     * @param string $number
     * @return void
     */
    public function processAssert(GiftWrappingIndex $giftWrappingIndex, $number)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_UPDATE_MESSAGE, $number),
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
        return 'Gift Wrapping success update message is present.';
    }
}
