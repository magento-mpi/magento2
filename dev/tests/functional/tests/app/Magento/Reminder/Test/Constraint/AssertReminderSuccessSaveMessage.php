<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reminder\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Reminder\Test\Page\Adminhtml\ReminderIndex;

/**
 * Assert message present "You saved the reminder rule.".
 */
class AssertReminderSuccessSaveMessage extends AbstractConstraint
{
    /**
     * Text success save reminder message.
     */
    const SUCCESS_SAVE_MESSAGE = 'You saved the reminder rule.';

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert message present "You saved the reminder rule.".
     *
     * @param ReminderIndex $reminderIndex
     * @return void
     */
    public function processAssert(ReminderIndex $reminderIndex)
    {
        $actualMessage = $reminderIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_SAVE_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message is displayed.';
    }
}
