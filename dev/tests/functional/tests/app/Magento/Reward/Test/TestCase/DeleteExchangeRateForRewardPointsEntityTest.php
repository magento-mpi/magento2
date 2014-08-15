<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Reward\Test\Fixture\RewardRate;
use Magento\Reward\Test\Page\Adminhtml\RewardRateNew;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Reward\Test\Page\Adminhtml\RewardRateIndex;

/**
 * Test Creation for Exchange Rate Deletion for RewardPointsEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Exchange rate (Points > Currency) is created.
 * 2. Exchange rate (Currency > Points) is created.
 *
 * Steps:
 * 1. Login to the backend.
 * 2. Navigate to Stores > Other Settings > Reward Exchange Rates
 * 3. Click on the exchange rate from preconditions.
 * 4. Click on the "Delete" button
 * 5. Perform appropriate assertions.
 *
 * @group Reward_Points_(CS)
 * @ZephyrId MAGETWO-26344
 */
class DeleteExchangeRateForRewardPointsEntityTest extends Injectable
{
    /**
     * Reward Rate Index page
     *
     * @var RewardRateIndex
     */
    protected $rewardRateIndex;

    /**
     * Reward Rate New page
     *
     * @var RewardRateNew
     */
    protected $rewardRateNew;

    /**
     * Preparing magento instance for whole test
     *
     * @param FixtureFactory $fixtureFactory
     * @param CustomerInjectable $customer
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory, CustomerInjectable $customer)
    {
        $configuration = $fixtureFactory->createByCode('configData', ['dataSet' => 'reward_purchase']);
        $configuration->persist();
        $customer->persist();

        return ['customer' => $customer];
    }

    /**
     * Preparing magento instance for test variation
     *
     * @param RewardRateIndex $rewardRateIndex
     * @param RewardRateNew $rewardRateNew
     * @return void
     */
    public function __inject(RewardRateIndex $rewardRateIndex, RewardRateNew $rewardRateNew)
    {
        $this->rewardRateIndex = $rewardRateIndex;
        $this->rewardRateNew = $rewardRateNew;

        // Check that Reward exchange Rates Grid is empty. Delete any rate if it exists
        $rewardRateIndex->open();
        while ($rewardRateIndex->getRewardRateGrid()->isFirstRowVisible()) {
            $rewardRateIndex->getRewardRateGrid()->openFirstRow();
            $rewardRateNew->getFormPageActions()->delete();
        }
    }

    /**
     * Run Test Creation for Exchange Rate Deletion for RewardRateEntity
     *
     * @param RewardRate $rate
     * @return void
     */
    public function test(RewardRate $rate)
    {
        // Preconditions
        $rate->persist();

        // Steps
        $filter = ['rate_id' => $rate->getRateId()];
        $this->rewardRateIndex->open();
        $this->rewardRateIndex->getRewardRateGrid()->searchAndOpen($filter);
        $this->rewardRateNew->getFormPageActions()->delete();
    }
}
