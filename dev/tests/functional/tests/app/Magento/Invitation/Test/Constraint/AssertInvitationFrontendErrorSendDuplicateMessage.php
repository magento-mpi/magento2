<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Invitation\Test\Page\InvitationIndex;

/**
 * Class AssertInvitationFrontendErrorSendDuplicateMessage
 * Assert that error message appears after sent invitation to the same email address
 */
class AssertInvitationFrontendErrorSendDuplicateMessage  extends AbstractConstraint
{
    /**
     * Error duplicate message
     */
    const ERROR_MESSAGE = "Invitation for same email address already exists.";

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that error message appears after sent invitation to the same email address
     *
     * @param InvitationIndex $invitationIndex
     * @return void
     */
    public function processAssert(InvitationIndex $invitationIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::ERROR_MESSAGE,
            $invitationIndex->getMessagesBlock()->getErrorMessages(),
            "Expected error duplicate message doesn't match actual."
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Error duplicate message appears on Invitation index frontend page.';
    }
}
