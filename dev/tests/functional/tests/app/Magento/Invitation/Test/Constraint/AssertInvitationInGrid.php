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
        $invitationGrid = $invitationsIndex->getInvitationGrid();
        $invitations = [];
        foreach ($invitation->getEmail() as $email) {
            $invitationGrid->search(['email' => $email]);
            $rowsData = $invitationGrid->getRowsData(['id', 'email']);
            foreach ($rowsData as $rowData) {
                $invitations[] = $rowData;
            }
        }
        $invitations = array_unique($invitations, SORT_REGULAR);

        $result = [];
        foreach ($invitations as $invitationData) {
            $filter = [
                'id' => $invitationData['id'],
                'email' => $invitationData['email'],
                'invitee_group' => $customer->getGroupId(),
                'status' => $status
            ];

            $invitationGrid->search($filter);
            $data = $invitationGrid->getRowsData(['id', 'email']);
            $result[] = array_shift($data);
        }

        $result = array_unique($result, SORT_REGULAR);
        foreach ($result as $key => $value) {
            $result[$key] = $value['email'];
        }
        \PHPUnit_Framework_Assert::assertEquals(array_values($invitation->getEmail()), $result);
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
