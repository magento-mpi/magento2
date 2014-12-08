<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\Constraint;

use Magento\Invitation\Test\Fixture\Invitation;
use Magento\Invitation\Test\Page\Adminhtml\InvitationsIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Assert that error message appears after sending invitation on backend.
 */
class AssertInvitationErrorSentMessage extends AbstractConstraint
{
    /**
     * Error send message.
     */
    const ERROR_MESSAGE = "Something went wrong sending %d of %d invitations.";

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that error message appears after sending invitation on backend.
     *
     * @param InvitationsIndex $invitationsIndex
     * @param Invitation $invitation
     * @param string $countNotSent
     * @return void
     */
    public function processAssert(InvitationsIndex $invitationsIndex, Invitation $invitation, $countNotSent)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::ERROR_MESSAGE, $countNotSent, count($invitation->getEmail())),
            $invitationsIndex->getMessagesBlock()->getErrorMessages(),
            "Expected error message doesn't match actual."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Error message appears on Invitations index backend page.';
    }
}
