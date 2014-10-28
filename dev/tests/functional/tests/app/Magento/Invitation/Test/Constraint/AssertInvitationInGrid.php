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
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Invitation\Test\Page\Adminhtml\InvitationsIndex;

/**
 * Class AssertInvitationInGrid.
 * Assert created invitation appears in Invitation grid on backend.
 */
class AssertInvitationInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert created invitation appears in Invitation grid on backend: email, status, Invitee.
     *
     * @param InvitationsIndex $invitationsIndex
     * @param Invitation $invitation
     * @param CustomerInjectable $customer
     * @param string $status
     * @return void
     */
    public function processAssert(
        InvitationsIndex $invitationsIndex,
        Invitation $invitation,
        CustomerInjectable $customer,
        $status
    ) {
        $invitationsIndex->open();
        $error = '';
        $invitationGrid = $invitationsIndex->getInvitationGrid();
        foreach ($invitation->getEmail() as $email) {
            $filter = [
                'email' => $email,
                'invitee_group' => $customer->getGroupId(),
                'status' => $status
            ];
            if (!$invitationGrid->isRowVisible($filter)) {
                $error .= "Email: {$email} with status: {$status} is not available in invitation grid.\n";
            }
        }
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Created invitation appears in invitation grid on backend.';
    }
}
