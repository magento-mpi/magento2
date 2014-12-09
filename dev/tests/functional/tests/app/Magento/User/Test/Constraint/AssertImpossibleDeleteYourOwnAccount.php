<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\User\Test\Page\Adminhtml\UserEdit;

/**
 * Class AssertImpossibleDeleteYourOwnAccount
 */
class AssertImpossibleDeleteYourOwnAccount extends AbstractConstraint
{
    const ERROR_MESSAGE = 'You cannot delete your own account.';

    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Asserts that error message equals to expected message.
     *
     * @param UserEdit $userEdit
     * @return void
     */
    public function processAssert(UserEdit $userEdit)
    {
        $errorMessage = $userEdit->getMessagesBlock()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::ERROR_MESSAGE,
            $errorMessage,
            'Wrong error message is displayed.'
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
        return '"You cannot delete self-assigned roles." message on UserEdit page is correct.';
    }
}
