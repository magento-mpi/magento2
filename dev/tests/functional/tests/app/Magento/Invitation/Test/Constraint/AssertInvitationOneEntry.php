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
        $loginCustomerOnFrontendStep = $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        );
        $loginCustomerOnFrontendStep->run();
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Invitations');
        $email = $invitation->getEmail()[1];
        $actualData = $invitationIndex->getInvitationsBlock()->getRowData($email, $status);
        \PHPUnit_Framework_Assert::assertTrue(
            count($actualData) == 1,
            "Invitation with email: {$email} and status: {$status} is absent in grid or its number is greater than 1."
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
