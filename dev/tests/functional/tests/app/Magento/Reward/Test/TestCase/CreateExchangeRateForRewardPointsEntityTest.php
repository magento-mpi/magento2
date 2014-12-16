<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Reward\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Core\Test\Fixture\ConfigData;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Reward\Test\Fixture\RewardRate;
use Magento\Reward\Test\Page\Adminhtml\RewardRateIndex;
use Magento\Reward\Test\Page\Adminhtml\RewardRateNew;
use Mtf\TestCase\Injectable;

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
 * @group Reward_Points_(CS)
 * @ZephyrId MAGETWO-24808
 */
class CreateExchangeRateForRewardPointsEntityTest extends Injectable
{
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
     * Configuration rollback data set
     *
     * @var ConfigData
     */
    protected $configRollback;

    /**
     * Prepare data
     *
     * @param CatalogProductSimple $product
     * @return array
     */
    public function __prepare(CatalogProductSimple $product)
    {
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
     * @param RewardRate $rate
     * @param CustomerInjectable $customer
     * @param ConfigData $config
     * @param ConfigData $configRollback
     * @return void
     */
    public function test(
        RewardRate $rate,
        CustomerInjectable $customer,
        ConfigData $config,
        ConfigData $configRollback
    ) {
        // Precondition
        $this->configRollback = $configRollback;
        $config->persist();
        $customer->persist();

        // Steps
        $this->rewardRateIndexPage->open()->getGridPageActions()->addNew();
        $this->rewardRateNewPage->getRewardRateForm()->fill($rate);
        $this->rewardRateNewPage->getFormPageActions()->save();
    }

    /**
     * Remove created exchange rates and rollback configuration
     *
     * @return void
     */
    public function tearDown()
    {
        $this->rewardRateIndexPage->open();
        while ($this->rewardRateIndexPage->getRewardRateGrid()->isFirstRowVisible()) {
            $this->rewardRateIndexPage->getRewardRateGrid()->openFirstRow();
            $this->rewardRateNewPage->getFormPageActions()->delete();
        }
        $this->configRollback->persist();
    }
}
