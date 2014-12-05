<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\FixtureFactory;

/**
 * Check that login again to frontend with new password was success.
 */
class AssertCustomerPasswordChanged extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that login again to frontend with new password was success.
     *
     * @param FixtureFactory $fixtureFactory
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerInjectable $initialCustomer
     * @param CustomerInjectable $customer
     * @return void
     */
    public function processAssert(
        FixtureFactory $fixtureFactory,
        CustomerAccountIndex $customerAccountIndex,
        CustomerInjectable $initialCustomer,
        CustomerInjectable $customer
    ) {
        $customer = $fixtureFactory->createByCode(
            'customerInjectable',
            [
                'dataSet' => 'default',
                'data' => [
                    'email' => $initialCustomer->getEmail(),
                    'password' => $customer->getPassword(),
                    'password_confirmation' => $customer->getPassword(),
                ],
            ]
        );

        $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        )->run();

        \PHPUnit_Framework_Assert::assertTrue(
            $customerAccountIndex->getAccountMenuBlock()->isVisible(),
            'Customer Account Dashboard is not visible.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer password was changed.';
    }
}
