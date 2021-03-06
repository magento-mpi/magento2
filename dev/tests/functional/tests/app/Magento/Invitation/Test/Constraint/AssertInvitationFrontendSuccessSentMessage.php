<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Invitation\Test\Constraint;

use Magento\Invitation\Test\Fixture\Invitation;
use Magento\Invitation\Test\Page\InvitationIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertInvitationFrontendSuccessSentMessage
 * Assert that success message appears after sent invitation on frontend
 */
class AssertInvitationFrontendSuccessSentMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success sent message
     */
    const SUCCESS_MESSAGE = "You sent the invitation for %s.";

    /**
     * Assert that success message appears after sent invitation on frontend
     *
     * @param Invitation $invitation
     * @param InvitationIndex $invitationIndex
     * @return void
     */
    public function processAssert(Invitation $invitation, InvitationIndex $invitationIndex)
    {
        $expectedMessages = [];
        foreach ($invitation->getEmail() as $email) {
            $expectedMessages[] = sprintf(self::SUCCESS_MESSAGE, $email);
        }
        \PHPUnit_Framework_Assert::assertEquals(
            $expectedMessages,
            $invitationIndex->getMessagesBlock()->getSuccessMessages(),
            "Expected success message doesn't match actual."
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message appears on Invitation index frontend page.';
    }
}
