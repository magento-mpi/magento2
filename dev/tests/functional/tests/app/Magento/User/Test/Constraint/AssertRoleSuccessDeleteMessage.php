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
 *
 * @package Magento\User\Test\Constraint
 */
class AssertRoleSuccessDeleteMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You deleted the role.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Asserts that success delete message equals to expected message.
     *
     * @return void
     */
    public function processAssert(UserRoleIndex $rolePage)
    {
        $successMessage = $rolePage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $successMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
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
