<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Invitation\Test\Fixture\Invitation;
use Magento\Invitation\Test\Page\Adminhtml\InvitationsIndex;

/**
 * Class AssertInvitationSuccessDiscardMessage.
 * Assert that success message appears after discard invitation on backend.
 */
class AssertInvitationSuccessDiscardMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success discard message.
     */
    const SUCCESS_MESSAGE = "We discarded %d of %d invitations.";

    /**
     * Assert that success message appears after discard invitation on backend.
     *
     * @param InvitationsIndex $invitationsIndex
     * @param Invitation $invitation
     * @param string $emailsCount
     * @return void
     */
    public function processAssert(InvitationsIndex $invitationsIndex, Invitation $invitation, $emailsCount)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_MESSAGE, $emailsCount, count($invitation->getEmail())),
            $invitationsIndex->getMessagesBlock()->getSuccessMessages(),
            "Expected success message doesn't match actual."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Success discard message appears on Invitations index backend page.';
    }
}
