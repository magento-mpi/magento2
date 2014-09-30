<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Magento\Reward\Test\Page\Adminhtml\RewardRateIndex;
use Magento\Reward\Test\Page\Adminhtml\RewardRateNew;
use Mtf\TestCase\Scenario;
use Mtf\Fixture\FixtureFactory;
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
     * Configuration data set name
     *
     * @var string
     */
    protected $configuration;

    /**
     * Factory for Fixtures
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

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
     * Steps for scenario
     *
     * @var array
     */
    protected $scenario = [
        'CreateOrderFromCustomerPageTest' => [
            'methods' => [
                'test' => [
                    'scenario' => [
                        'createCustomer' => [
                            'module' => 'Magento_Customer',
                            'next' => 'createRewardExchangeRates'
                        ],
                        'createRewardExchangeRates' => [
                            'module' => 'Magento_Reward',
                            'arguments' => [
                                'rewardRates' => ['rate_points_to_currency', 'rate_currency_to_points'],
                            ],
                            'next' => 'createProducts'
                        ],
                        'applyRewardPointsToCustomer' => [
                            'module' => 'Magento_Reward',
                            'next' => 'createProducts'
                        ],
                        'createProducts' => [
                            'module' => 'Magento_Catalog',
                            'next' => 'createSalesRule'
                        ],
                        'createSalesRule' => [
                            'module' => 'Magento_SalesRule',
                            'next' => 'openCustomerAccount'
                        ],
                        'openCustomerAccount' => [
                            'module' => 'Magento_Customer',
                            'next' => 'createOrderFromCustomerAccount'
                        ],
                        'createOrderFromCustomerAccount' => [
                            'module' => 'Magento_Sales',
                            'next' => 'addProducts'
                        ],
                        'addProducts' => [
                            'module' => 'Magento_Sales',
                            'next' => 'updateProductsData'
                        ],
                        'updateProductsData' => [
                            'module' => 'Magento_Sales',
                            'next' => 'applySalesRuleOnBackend'
                        ],
                        'applySalesRuleOnBackend' => [
                            'module' => 'Magento_SalesRule',
                            'next' => 'fillBillingAddress',
                        ],
                        'fillBillingAddress' => [
                            'module' => 'Magento_Sales',
                            'next' => 'selectPaymentMethodForOrder'
                        ],
                        'selectPaymentMethodForOrder' => [
                            'module' => 'Magento_Sales',
                            'next' => 'selectShippingMethodForOrder'
                        ],
                        'selectShippingMethodForOrder' => [
                            'module' => 'Magento_Sales',
                            'next' => 'submitOrder'
                        ],
                        'submitOrder' => [
                            'module' => 'Magento_Sales',
                        ],
                    ]
                ]
            ]
        ]
    ];

    /**
     * Preparing configuration for test
     *
     * @param FixtureFactory $fixtureFactory
     * @param RewardRateIndex $rewardRateIndexPage
     * @param RewardRateNew $rewardRateNewPage
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __prepare(
        FixtureFactory $fixtureFactory,
        RewardRateIndex $rewardRateIndexPage,
        RewardRateNew $rewardRateNewPage,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->fixtureFactory = $fixtureFactory;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->rewardRateIndexPage = $rewardRateIndexPage;
        $this->rewardRateNewPage = $rewardRateNewPage;
    }

    /**
     * Runs sales order on backend
     *
     * @param string $config
     * @return void
     */
    public function test($config)
    {
        $this->configuration = $config;
        if ($this->configuration !== '-') {
            $this->setupConfiguration();
        }
        $this->executeScenario($this->scenario);
    }

    /**
     * Setup configuration
     *
     * @param bool $rollback
     * @return void
     */
    protected function setupConfiguration($rollback = false)
    {
        $prefix = ($rollback == false) ? '' : '_rollback';
        $dataSets = explode(',', $this->configuration);

        foreach ($dataSets as $key => $dataSet) {
            $dataSets[$key] = trim($dataSet) . $prefix;
        }
        $setConfigStep = $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => implode(',', $dataSets)]
        );
        $setConfigStep->run();
    }

    /**
     * Disable enabled config after test
     *
     * @return void
     */
    public function tearDown()
    {
        if ($this->configuration !== '-') {
            $this->setupConfiguration(true);
        }
        $this->customerAccountLogout->open();

        // Deleting exchange rates
        $this->rewardRateIndexPage->open();
        while ($this->rewardRateIndexPage->getRewardRateGrid()->isFirstRowVisible()) {
            $this->rewardRateIndexPage->getRewardRateGrid()->openFirstRow();
            $this->rewardRateNewPage->getFormPageActions()->delete();
        }
    }
}
