<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\User\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\User\Test\Page\Adminhtml\UserEdit;
use Magento\User\Test\Fixture\User;

/**
 * Class AssertUserInvalidEmailMessage
 */
class AssertUserInvalidEmailMessage extends AbstractConstraint
{
    const ERROR_MESSAGE = 'Please correct this email address: "%s".';

    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Asserts that error message equals to expected message.
     *
     * @param UserEdit $userEdit
     * @param User $user
     * @return void
     */
    public function processAssert(UserEdit $userEdit, User $user)
    {
        $expectedMessage = sprintf(self::ERROR_MESSAGE, $user->getEmail());
        $actualMessage = $userEdit->getMessagesBlock()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            $expectedMessage,
            $actualMessage,
            'Wrong error message is displayed.'
            . "\nExpected: " . $expectedMessage
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Returns message if equals to expected message.
     *
     * @return string
     */
    public function toString()
    {
        return 'Error message about invalid email on creation user page is correct.';
    }
}
