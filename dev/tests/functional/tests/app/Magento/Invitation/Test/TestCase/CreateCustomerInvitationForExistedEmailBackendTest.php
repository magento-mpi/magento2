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
use Magento\Invitation\Test\Fixture\Invitation;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Invitation\Test\Page\Adminhtml\InvitationsIndex;
use Magento\Invitation\Test\Page\Adminhtml\InvitationsIndexNew;

/**
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer.
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
     * Create customer invitation for existed email on backend test
     *
     * @param CustomerInjectable $customer
     * @param Invitation $invitation
     * @param array $data
     * @return void
     */
    public function test(CustomerInjectable $customer, Invitation $invitation, array $data)
    {
        // Preconditions
        $customer->persist();
        $preparedData['email']['email_1'] = $customer->getEmail();
        $preparedData['email']['email_2'] = $invitation->getEmail()['email_1'];
        $data = array_merge($data, $preparedData);
        $fixtureData = $invitation->getData();
        $data = array_merge($data, $fixtureData);
        $data['email'] = implode(',', $preparedData['email']);
        $invitation =  $this->fixtureFactory->createByCode('invitation', ['data' => $data]);

        // Steps
        $this->invitationsIndex->open();
        $this->invitationsIndex->getGridPageActions()->addNew();
        $this->invitationsIndexNew->getFormBlock()->fill($invitation);
        $this->invitationsIndexNew->getPageMainActions()->save();
    }
}
