<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Invitation\Test\Page\Adminhtml\InvitationsIndex;
use Magento\Invitation\Test\Page\Adminhtml\InvitationsIndexNew;

/**
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer.
 * 2. Create invitation.
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
 * @ZephyrId MAGETWO-29948
 */
class CreateCustomerInvitationForExistedEmailBackendTest extends Injectable
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
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Injection data.
     *
     * @param InvitationsIndex $invitationsIndex
     * @param InvitationsIndexNew $invitationsIndexNew
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        InvitationsIndex $invitationsIndex,
        InvitationsIndexNew $invitationsIndexNew,
        FixtureFactory $fixtureFactory
    ) {
        $this->invitationsIndex = $invitationsIndex;
        $this->invitationsIndexNew = $invitationsIndexNew;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Create customer invitation for existed email on backend test.
     *
     * @param CustomerInjectable $customer
     * @param array $invitationData
     * @return array
     */
    public function test(CustomerInjectable $customer, array $invitationData)
    {
        // Preconditions
        $customer->persist();
        $invitationData['email'] = $invitationData['email'] . ', ' . $customer->getEmail();
        $invitation = $this->fixtureFactory->createByCode('invitation', ['data' => $invitationData]);

        // Steps
        $this->invitationsIndex->open();
        $this->invitationsIndex->getGridPageActions()->addNew();
        $this->invitationsIndexNew->getFormBlock()->fill($invitation);
        $this->invitationsIndexNew->getPageMainActions()->save();

        return ['invitation' => $invitation];
    }
}
