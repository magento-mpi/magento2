<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingIndex;

/**
 * Class AssertGiftWrappingSuccessDeleteMessage
 * Assert that after delete Gift Wrapping successful message appears
 */
class AssertGiftWrappingSuccessDeleteMessage extends AbstractConstraint
{
    /**
     * Message displayed after delete gift wrapping
     */
    const SUCCESS_DELETE_MESSAGE = 'You deleted the gift wrapping.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

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
