<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Magento\Customer\Test\Page\CustomerAccountLogout;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Scenario;

/**
 * Test Creation for CreateOrderBackendTest
 *
 * Test Flow:
 * Preconditions:
 * 1. Create customer
 * 2. Create product
 *
 * Steps:
 * 1. Open Backend
 * 2. Open Sales -> Orders
 * 3. Click Create New Order
 * 4. Select Customer created in preconditions
 * 5. Add Product
 * 6. Fill data according dataSet
 * 7. Click Update Product qty
 * 8. Fill data according dataSet
 * 9. Click Get Shipping Method and rates
 * 10. Fill data according dataSet
 * 11. Submit Order
 * 12. Perform all assertions
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-28696
 */
class CreateOrderBackendTest extends Scenario
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
     * Steps for scenario
     *
     * @var array
     */
    protected $scenario = [
        'CreateOrderBackendTest' => [
            'methods' => [
                'test' => [
                    'scenario' => [
                        'createCustomer' => [
                            'module' => 'Magento_Customer',
                            'arguments' => [
                                'customer' => ['dataSet' => 'johndoe_with_addresses'],
                            ],
                            'next' => 'createProducts'
                        ],
                        'createProducts' => [
                            'module' => 'Magento_Catalog',
                            'next' => 'createSalesRule'
                        ],
                        'createSalesRule' => [
                            'module' => 'Magento_SalesRule',
                            'next' => 'openSalesOrders'
                        ],
                        'openSalesOrders' => [
                            'module' => 'Magento_Sales',
                            'next' => 'createNewOrder'
                        ],
                        'createNewOrder' => [
                            'module' => 'Magento_Sales',
                            'next' => 'selectCustomerOrder'
                        ],
                        'selectCustomerOrder' => [
                            'module' => 'Magento_Sales',
                            'next' => 'selectStore'
                        ],
                        'selectStore' => [
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
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __prepare(
        FixtureFactory $fixtureFactory,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->fixtureFactory = $fixtureFactory;
        $this->customerAccountLogout = $customerAccountLogout;
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
}
