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
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Reward\Test\Page\Adminhtml\RewardRateNew;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Reward\Test\Page\Adminhtml\RewardRateIndex;

/**
 * Test Creation for CreateExchangeRateForRewardPointsEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Product is created.
 * 2. Register new customer.
 *
 * Steps to reproduce:
 * 1. Login to the backend.
 * 2. Navigate to Stores > Other Settings > Reward Exchange Rates.
 * 3. Click on the "Add New Rate" button.
 * 4. Fill in data according to attached data set.
 * 5. Save Reward Exchange Rate.
 * 6. Perform appropriate assertions.
 *
 * @group Product_Attributes_(MX), Reward_Points_(CS)
 * @ZephyrId MAGETWO-24808
 */
class CreateExchangeRateForRewardPointsEntityTest extends Injectable
{
    /**
     * Factory for fixture
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Index page reward exchange rates
     *
     * @var RewardRateIndex
     */
    protected $rewardRateIndexPage;

    /**
     * Page new reward exchange rate
     *
     * @var RewardRateNew
     */
    protected $rewardRateNewPage;

    /**
     * Configuration data set
     *
     * @var string
     */
    protected $config;

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
        $product = $fixtureFactory->createByCode('catalogProductSimple');
        $product->persist();

        return ['product' => $product];
    }

    /**
     * Injection data
     *
     * @param RewardRateIndex $rewardRateIndexPage
     * @param RewardRateNew $rewardRateNewPage
     * @return void
     */
    public function __inject(
        RewardRateIndex $rewardRateIndexPage,
        RewardRateNew $rewardRateNewPage
    ) {
        $this->rewardRateIndexPage = $rewardRateIndexPage;
        $this->rewardRateNewPage = $rewardRateNewPage;
    }

    /**
     * Run create exchange rate for reward points entity
     *
     * @param Reward $rate
     * @param CustomerInjectable $customer
     * @param string $config
     * @param string $registrationReward
     * @param string $checkoutReward
     * @return void
     */
    public function test(
        Reward $rate,
        CustomerInjectable $customer,
        $config,
        $registrationReward,
        $checkoutReward
    ) {
        // Precondition
        $this->config = $config;
        $configData = $this->fixtureFactory->createByCode('configData', ['dataSet' => $this->config]);
        $configData->persist();
        $customer->persist();

        // Steps
        $this->rewardRateIndexPage->open()->getGridActions()->addNew();
        $this->rewardRateNewPage->getForm()->fill($rate);
        $this->rewardRateNewPage->getFormPageActions()->save();
    }

    /**
     * Remove created exchange rates and rlollback configuration
     *
     * @return void
     */
    public function tearDown()
    {
        $this->rewardRateIndexPage->open();
        while ($this->rewardRateIndexPage->getGridRate()->isFirstRowVisible()) {
            $this->rewardRateIndexPage->getGridRate()->openFirstRow();
            $this->rewardRateNewPage->getFormPageActions()->delete();
        }
        $config = $this->config . '_rollback';
        $configData = $this->fixtureFactory->createByCode('configData', ['dataSet' => $config]);
        $configData->persist();
    }
}
