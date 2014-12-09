<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\User\Test\Page\Adminhtml\UserRoleEditRole;

/**
 * Class AssertRoleSuccessSaveMessage
 */
class AssertImpossibleDeleteYourOwnRole extends AbstractConstraint
{
    const ERROR_MESSAGE = 'You cannot delete self-assigned roles.';

    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Asserts that error message equals to expected message.
     *
     * @param UserRoleEditRole $rolePage
     * @return void
     */
    public function processAssert(UserRoleEditRole $rolePage)
    {
        $errorMessage = $rolePage->getMessagesBlock()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::ERROR_MESSAGE,
            $errorMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::ERROR_MESSAGE
            . "\nActual: " . $errorMessage
        );
    }

    /**
     * Returns message if equals to expected message.
     *
     * @return string
     */
    public function toString()
    {
        return '"You cannot delete self-assigned roles." message on EditRole page is correct.';
    }
}
