<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\TestStep;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestStep\TestStepInterface;

/**
 * Class ApplyCustomerBalanceToCustomerStep
 * Apply customer balance to customer
 */
class ApplyCustomerBalanceToCustomerStep implements TestStepInterface
{
    /**
     * Customer fixture
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * Customer Balance amount
     *
     * @var string
     */
    protected $customerBalance;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param CustomerInjectable $customer
     * @param string $customerBalance
     */
    public function __construct(FixtureFactory $fixtureFactory, CustomerInjectable $customer, $customerBalance)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->customerBalance = $customerBalance;
        $this->customer = $customer;
    }

    /**
     * Apply customer balance to customer
     *
     * @return void
     */
    public function run()
    {
        if ($this->customerBalance != '-') {
            $customerBalance = $this->fixtureFactory->createByCode(
                'customerBalance',
                [
                    'dataSet' => $this->customerBalance,
                    'data' => [
                        'customer_id' => ['customer' => $this->customer],
                    ]
                ]
            );
            $customerBalance->persist();
        }
    }
}
