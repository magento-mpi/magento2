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
use Magento\Invitation\Test\Page\Adminhtml\InvitationsIndexView;

/**
 * Class AssertInvitationForm.
 * Assert that Invitation form was filled correctly.
 */
class AssertInvitationForm extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that Invitation form was filled correctly: email, message, status.
     *
     * @param InvitationsIndex $invitationsIndex
     * @param InvitationsIndexView $invitationsIndexView
     * @param array $invitation
     * @param string $status
     * @return void
     */
    public function processAssert(
        InvitationsIndex $invitationsIndex,
        InvitationsIndexView $invitationsIndexView,
        array $invitation,
        $status
    ) {
        $invitationsIndex->open();
        foreach ($invitation['email'] as $email) {
            $filter = [
                'email' => $email,
                'status' => $status,
            ];
            $invitationsIndex->getInvitationGrid()->searchAndOpen($filter);
            $expectedData = [
                'Email' => $email,
                'Invitation Message' => $invitation['message'],
                'Status' => $status
            ];
            $actualData = $invitationsIndexView->getGeneralTab()->getInvitationData();
            $result = array_intersect($expectedData, $actualData);
            \PHPUnit_Framework_Assert::assertEquals(
                $expectedData,
                $result,
                "Expected result doesn't match actual."
            );
            $invitationsIndexView->getPageActions()->back();
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Created invitation appears in invitation grid on backend.';
    }
}
