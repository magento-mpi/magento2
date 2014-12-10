<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\User\Test\Page\Adminhtml\UserRoleIndex;

/**
 * Class AssertRoleSuccessDeleteMessage
 */
class AssertRoleSuccessDeleteMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    const SUCCESS_DELETE_MESSAGE = 'You deleted the role.';

    /**
     * Asserts that success delete message equals to expected message.
     *
     * @param UserRoleIndex $rolePage
     * @return void
     */
    public function processAssert(UserRoleIndex $rolePage)
    {
        $successMessage = $rolePage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $successMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_DELETE_MESSAGE
            . "\nActual: " . $successMessage
        );
    }

    /**
     * Returns success delete message if equals to expected message.
     *
     * @return string
     */
    public function toString()
    {
        return 'Success delete message on roles page is correct.';
    }
}
