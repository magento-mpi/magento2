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
use Magento\Invitation\Test\Page\InvitationIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class AssertInvitationOneEntry
 * Assert only one invitation was sent to unique email on frontend
 */
class AssertInvitationOneEntry extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert only one invitation was sent to unique email on frontend
     *
     * @param CustomerInjectable $customer
     * @param Invitation $invitation
     * @param CustomerAccountIndex $customerAccountIndex
     * @param InvitationIndex $invitationIndex
     * @param string $status
     * @return void
     */
    public function processAssert(
        CustomerInjectable $customer,
        Invitation $invitation,
        CustomerAccountIndex $customerAccountIndex,
        InvitationIndex $invitationIndex,
        $status
    ) {
        $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        )->run();
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Invitations');
        $email = $invitation->getEmail()['email_1'];
        $count = $invitationIndex->getInvitationsBlock()->countInvitations($email, $status);
        \PHPUnit_Framework_Assert::assertTrue(
            $count == 1,
            "The number of invitation with email: {$email[1]} and status: {$status} is not equal to 1."
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Only one invitation was sent to unique email on frontend.';
    }
}
