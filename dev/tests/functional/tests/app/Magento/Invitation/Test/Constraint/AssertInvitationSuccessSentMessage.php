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
 * Class AssertInvitationSuccessSentMessage.
 * Assert that success message appears after sent invitation on backend.
 */
class AssertInvitationSuccessSentMessage extends AbstractConstraint
{
    /**
     * Success sent message.
     */
    const SUCCESS_MESSAGE = "We sent %d of %d invitations.";

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message appears after sent invitation on frontend.
     *
     * @param InvitationsIndex $invitationsIndex
     * @param string $number
     * @param string $total
     * @return void
     */
    public function processAssert(InvitationsIndex $invitationsIndex, $number, $total)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_MESSAGE, $number, $total),
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
