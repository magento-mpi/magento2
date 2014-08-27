<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\TestStep;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestStep\TestStepInterface;

/**
 * Class ApplyRewardPointsToCustomerStep
 * Apply reward points to customer
 */
class ApplyRewardPointsToCustomerStep implements TestStepInterface
{
    /**
     * Customer fixture
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * Reward points amount
     *
     * @var string
     */
    protected $rewardPoints;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param CustomerInjectable $customer
     * @param string $rewardPoints
     */
    public function __construct(FixtureFactory $fixtureFactory, CustomerInjectable $customer, $rewardPoints)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->rewardPoints = $rewardPoints;
        $this->customer = $customer;
    }

    /**
     * Apply reward points to customer
     *
     * @return void
     */
    public function run()
    {
        if ($this->rewardPoints != '-') {
            $reward = $this->fixtureFactory->createByCode(
                'reward',
                [
                    'dataSet' => $this->rewardPoints,
                    'data' => [
                        'customer_id' => ['customer' => $this->customer],
                    ]
                ]
            );
            $reward->persist();
        }
    }
}
