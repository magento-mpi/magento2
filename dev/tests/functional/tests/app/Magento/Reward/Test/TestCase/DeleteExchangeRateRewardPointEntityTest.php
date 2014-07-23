<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Reward\Test\Fixture\Reward;
use Magento\Reward\Test\Page\Adminhtml\ExchangeRateIndex;
use Magento\Reward\Test\Page\Adminhtml\ExchangeRateNew;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

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
class DeleteExchangeRateRewardPointEntityTest extends Injectable
{
    /**
     * ExchangeRateIndex page
     *
     * @var ExchangeRateIndex
     */
    protected $exchangeRateIndex;

    /**
     * ExchangeRateNew page
     *
     * @var ExchangeRateNew
     */
    protected $exchangeRateNew;

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
     * @param ExchangeRateIndex $exchangeRateIndex
     * @param ExchangeRateNew $exchangeRateNew
     * @return void
     */
    public function __inject(ExchangeRateIndex $exchangeRateIndex, ExchangeRateNew $exchangeRateNew)
    {
        $this->exchangeRateIndex = $exchangeRateIndex;
        $this->exchangeRateNew = $exchangeRateNew;

        // Check that Reward exchange Rates Grid is empty. Delete any rate if it exists
        $exchangeRateIndex->open();
        while ($exchangeRateIndex->getExchangeRateGrid()->isFirstRowVisible()) {
            $exchangeRateIndex->getExchangeRateGrid()->clickOnFirstRow();
            $exchangeRateNew->getFormPageActions()->delete();
        }
    }

    /**
     * Run Test Creation for Exchange Rate Deletion for RewardPointsEntity
     *
     * @param Reward $reward
     * @return void
     */
    public function test(Reward $reward)
    {
        // Preconditions
        $reward->persist();

        // Steps
        $filter = ['rate_id' => $reward->getRateId()];
        $this->exchangeRateIndex->open();
        $this->exchangeRateIndex->getExchangeRateGrid()->searchAndOpen($filter);
        $this->exchangeRateNew->getFormPageActions()->delete();
    }
}
