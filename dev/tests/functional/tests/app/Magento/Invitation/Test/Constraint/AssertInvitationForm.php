<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Magento\Invitation\Test\Fixture\Invitation;
use Magento\Invitation\Test\Page\Adminhtml\InvitationsIndex;
use Magento\Invitation\Test\Page\Adminhtml\InvitationsIndexView;

/**
 * Class AssertInvitationForm.
 * Assert that Invitation form was filled correctly.
 */
class AssertInvitationForm extends AbstractAssertForm
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
     * @param Invitation $invitation
     * @param string $status
     * @return void
     */
    public function processAssert(
        InvitationsIndex $invitationsIndex,
        InvitationsIndexView $invitationsIndexView,
        Invitation $invitation,
        $status
    ) {
        $invitationsIndex->open();
        foreach ($invitation->getEmail() as $email) {
            $filter = [
                'email' => $email,
                'status' => $status,
            ];
            $invitationsIndex->getInvitationGrid()->searchAndOpen($filter);
            $fixtureData = [
                'email' => $email,
                'message' => $invitation->getMessage(),
                'status' => $status
            ];
            $formData = $invitationsIndexView->getInvitationForm()->getData();
            $error = $this->verifyData($fixtureData, $formData);
            \PHPUnit_Framework_Assert::assertEmpty($error, $error);
            $invitationsIndexView->getFormPageActions()->back();
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Invitation data on View Invitation page on backend equals to passed from fixture.';
    }
}
