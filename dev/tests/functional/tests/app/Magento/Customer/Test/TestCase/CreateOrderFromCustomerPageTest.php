<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestCase;

use Mtf\TestCase\Scenario;
use Magento\Reward\Test\Page\Adminhtml\RewardRateIndex;
use Magento\Reward\Test\Page\Adminhtml\RewardRateNew;
use Magento\Customer\Test\Page\CustomerAccountLogout;

/**
 * Test Creation for CreateOrderFromCustomerPage
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Customers -> All Customers
 * 3. Select and open customer from the grid
 * 4. Click Create Order button
 * 5. Click Add Products
 * 6. Fill data according dataSet
 * 7. Click Update Product qty
 * 8. Fill data according dataSet
 * 9. Click Get Shipping Method and rates
 * 10. Fill data according dataSet
 * 11. Click Submit Order
 * 12. Perform all assertions
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-28960
 */
class CreateOrderFromCustomerPageTest extends Scenario
{
    /**
     * Customer logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Reward rate index page
     *
     * @var RewardRateIndex
     */
    protected $rewardRateIndexPage;

    /**
     * Reward rate new page
     *
     * @var RewardRateNew
     */
    protected $rewardRateNewPage;

    /**
     * Preparing configuration for test
     *
     * @param RewardRateIndex $rewardRateIndexPage
     * @param RewardRateNew $rewardRateNewPage
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __prepare(
        RewardRateIndex $rewardRateIndexPage,
        RewardRateNew $rewardRateNewPage,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->customerAccountLogout = $customerAccountLogout;
        $this->rewardRateIndexPage = $rewardRateIndexPage;
        $this->rewardRateNewPage = $rewardRateNewPage;
    }

    /**
     * Runs sales order on backend
     *
     * @return void
     */
    public function test()
    {
        $this->executeScenario();
    }

    /**
     * Disable enabled config after test
     *
     * @return void
     */
    public function tearDown()
    {
        $setConfigStep = $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => $this->currentVariation['arguments']['configData'], 'rollback' => true]
        );
        $setConfigStep->run();
        $this->customerAccountLogout->open();

        // Deleting exchange rates
        $this->rewardRateIndexPage->open();
        while ($this->rewardRateIndexPage->getRewardRateGrid()->isFirstRowVisible()) {
            $this->rewardRateIndexPage->getRewardRateGrid()->openFirstRow();
            $this->rewardRateNewPage->getFormPageActions()->delete();
        }
    }
}
