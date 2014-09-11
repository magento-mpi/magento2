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
 * Class AssertGiftWrappingSuccessUpdateMessage
 * Assert that after update Gift Wrapping successful message appears
 */
class AssertGiftWrappingSuccessUpdateMessage extends AbstractConstraint
{
    /**
     * Message displayed after update gift wrapping
     */
    const SUCCESS_UPDATE_MESSAGE = 'You updated a total of %d records.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

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
