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
 * Assert that notice message appears after sending invitation on backend.
 */
class AssertInvitationNoticeMessage extends AbstractConstraint
{
    // @codingStandardsIgnoreStart
    /**
     * Notice message.
     */
    const NOTICE_MESSAGE = "%d invitation(s) were not sent, because customer accounts already exist for specified email addresses.";
    // @codingStandardsIgnoreEnd

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that notice message appears after sending invitation on backend.
     *
     * @param InvitationsIndex $invitationsIndex
     * @param string $countNotSent
     * @return void
     */
    public function processAssert(InvitationsIndex $invitationsIndex, $countNotSent)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::NOTICE_MESSAGE, $countNotSent),
            $invitationsIndex->getMessagesBlock()->getNoticeMessages(),
            "Expected notice message doesn't match actual."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Notice message appears on Invitations index backend page.';
    }
}
