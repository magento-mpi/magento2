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
 * Class AssertUserSuccessSaveMessage
 */
class AssertUserSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You saved the user.';

    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Asserts that success message equals to expected message.
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
     * Returns success message if equals to expected message.
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message on User Index page is correct.';
    }
}
