<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Invitation\Test\Constraint;

use Magento\Invitation\Test\Page\InvitationIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertInvitationFrontendErrorSendDuplicateMessage
 * Assert that error message appears after sent invitation to the same email address
 */
class AssertInvitationFrontendErrorSendDuplicateMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Error duplicate message
     */
    const ERROR_MESSAGE = "Invitation for same email address already exists.";

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
