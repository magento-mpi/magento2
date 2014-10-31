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
use Magento\Invitation\Test\Page\InvitationSend;
use Magento\Invitation\Test\Page\InvitationIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Test Creation for CreateCustomerInvitationFrontendEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer
 *
 * Steps:
 * 1. Login to frontend as customer created in preconditions
 * 2. Navigate to My Account -> My Invitations
 * 3. Click Send Invitations
 * 4. Fill data according to dataSet
 * 5. Click Send
 * 6. Perform all assertions
 *
 * @group Invitations_(CS)
 * @ZephyrId MAGETWO-29607
 */
class CreateCustomerInvitationFrontendEntityTest extends Injectable
{
    /**
     * CustomerAccountIndex page
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

    /**
     * InvitationIndex page
     *
     * @var InvitationIndex
     */
    protected $invitationIndex;

    /**
     * InvitationSend page
     *
     * @var InvitationSend
     */
    protected $invitationSend;

    /**
     * Injection data
     *
     * @param CustomerAccountIndex $customerAccountIndex
     * @param InvitationIndex $invitationIndex
     * @param InvitationSend $invitationSend
     * @return void
     */
    public function __inject(
        CustomerAccountIndex $customerAccountIndex,
        InvitationIndex $invitationIndex,
        InvitationSend $invitationSend
    ) {
        $this->customerAccountIndex = $customerAccountIndex;
        $this->invitationIndex = $invitationIndex;
        $this->invitationSend = $invitationSend;
    }

    /**
     * Create customer invitation frontend test
     *
     * @param CustomerInjectable $customer
     * @param Invitation|string $invitation
     * @return void
     */
    public function test(CustomerInjectable $customer, Invitation $invitation)
    {
        // Preconditions
        $customer->persist();

        // Steps
        $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        )->run();
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Invitations');
        $this->invitationIndex->getInvitationsBlock()->sendInvitations();
        $this->invitationSend->getInvitationsBlock()->fill($invitation);
        $this->invitationSend->getInvitationsBlock()->sendInvitations();
    }
}
