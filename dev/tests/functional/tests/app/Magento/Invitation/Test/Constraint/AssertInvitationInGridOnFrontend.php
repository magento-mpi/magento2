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
 * Class AssertInvitationInGridOnFrontend
 * Assert Invitation appears on frontend in My Invitations grid
 */
class AssertInvitationInGridOnFrontend extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert Invitation appears on frontend in My Invitations grid
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
        $expectedData = $invitation->getEmail();
        $actualData = $invitationIndex->getInvitationsBlock()->getAvailableEmails($expectedData, $status);
        \PHPUnit_Framework_Assert::assertEquals($expectedData, $actualData);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Invitation appears in My invitation grid on frontend.';
    }
}
