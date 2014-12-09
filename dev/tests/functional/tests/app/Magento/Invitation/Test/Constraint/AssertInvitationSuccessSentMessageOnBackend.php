<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Invitation\Test\Page\Adminhtml\InvitationsIndex;

/**
 * Assert that success message appears after sent invitation on backend.
 */
class AssertInvitationSuccessSentMessageOnBackend extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success sent message.
     */
    const SUCCESS_MESSAGE = "We sent %d invitation(s).";

    /**
     * Assert that success message appears after sent invitation on frontend.
     *
     * @param InvitationsIndex $invitationsIndex
     * @param string $countSent
     * @return void
     */
    public function processAssert(InvitationsIndex $invitationsIndex, $countSent)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_MESSAGE, $countSent),
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
        return 'Success send message appears on Invitations index backend page.';
    }
}
