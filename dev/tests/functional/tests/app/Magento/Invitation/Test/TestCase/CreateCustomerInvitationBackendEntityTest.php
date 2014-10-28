<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Invitation\Test\Fixture\Invitation;
use Magento\Invitation\Test\Page\Adminhtml\InvitationsIndex;
use Magento\Invitation\Test\Page\Adminhtml\InvitationsIndexNew;

/**
 * Test Flow:
 *
 * Steps:
 * 1. Open Backend.
 * 2. Navigate to Marketing > Invitations.
 * 3. Create New Invitation.
 * 4. Fill data according to dataSet.
 * 5. Save Invitation.
 * 6. Perform all assertions.
 *
 * @group Invitations_(CS)
 * @ZephyrId MAGETWO-29925
 */
class CreateCustomerInvitationBackendEntityTest extends Injectable
{
    /**
     * InvitationsIndex Page.
     *
     * @var InvitationsIndex
     */
    protected $invitationsIndex;

    /**
     * InvitationsIndexNew Page.
     *
     * @var InvitationsIndexNew
     */
    protected $invitationsIndexNew;

    /**
     * Injection data.
     *
     * @param InvitationsIndex $invitationsIndex
     * @param InvitationsIndexNew $invitationsIndexNew
     * @return void
     */
    public function __inject(
        InvitationsIndex $invitationsIndex,
        InvitationsIndexNew $invitationsIndexNew
    ) {
        $this->invitationsIndex = $invitationsIndex;
        $this->invitationsIndexNew = $invitationsIndexNew;
    }

    /**
     * Create customer invitation backend entity test
     *
     * @param Invitation $invitation
     * @return void
     */
    public function test(Invitation $invitation)
    {
        $this->invitationsIndex->open();
        $this->invitationsIndex->getGridPageActions()->addNew();
        $this->invitationsIndexNew->getFormBlock()->fill($invitation);
        $this->invitationsIndexNew->getPageMainActions()->save();
    }
}
