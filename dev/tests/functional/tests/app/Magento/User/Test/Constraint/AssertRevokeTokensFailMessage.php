<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Constraint;

use Magento\User\Test\Page\Adminhtml\UserEdit;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertRevokeTokensFailMessage
 * Assert that error message appears: "This user has no tokens.".
 */
class AssertRevokeTokensFailMessage extends AbstractConstraint
{
    /**
     * User revoke tokens error message.
     */
    const ERROR_MESSAGE = 'This user has no tokens.';

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that error message appears: "This user has no tokens.".
     *
     * @param UserEdit $userEdit
     * @return void
     */
    public function processAssert(UserEdit $userEdit)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::ERROR_MESSAGE,
            $userEdit->getMessagesBlock()->getErrorMessages()
        );
    }

    /**
     * Return string representation of object
     *
     * @return string
     */
    public function toString()
    {
        return '"This user has no tokens." error message is present on UserEdit page.';
    }
}
