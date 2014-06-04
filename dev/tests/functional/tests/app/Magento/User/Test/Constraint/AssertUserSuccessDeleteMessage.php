<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\User\Test\Page\Adminhtml\UserIndex;

/**
 * Class AssertUserSuccessDeleteMessage
 */
class AssertUserSuccessDeleteMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You deleted the user.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Asserts that success delete message equals to expected message.
     *
     * @param UserIndex $userIndex
     * @return void
     */
    public function processAssert(UserIndex $userIndex)
    {
        $successMessage = $userIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $successMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $successMessage
        );
    }

    /**
     * Returns message if success message equals to expected message.
     *
     * @return string
     */
    public function toString()
    {
        return 'Success delete message on users page is correct.';
    }
}
