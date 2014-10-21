<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\TestCase;

use Magento\Customer\Test\Page\CustomerAccountLogout;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Scenario;

/**
 * Test Creation for CreateGiftMessageOnBackend
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create Product according dataset.
 * 2. Enable Gift Messages (Order/Items level).
 *
 * Steps:
 * 1. Login to backend
 * 2. Go to Sales >Orders
 * 3. Create new order
 * 4. Fill data form dataSet
 * 5. Perform all asserts
 *
 * @group Gift_Messages_(CS)
 * @ZephyrId MAGETWO-29642
 */
class CreateGiftMessageOnBackendTest extends Scenario
{
    /**
     * Configuration data set name.
     *
     * @var string
     */
    protected $configuration;

    /**
     * Factory for Fixtures.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Customer logout page.
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Steps for scenario.
     *
     * @var array
     */
    protected $scenario = [
        'CreateGiftMessageOnBackendTest' => [
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
                            'next' => 'addGiftMessageBackend'
                        ],
                        'addGiftMessageBackend' => [
                            'module' => 'Magento_GiftMessage',
                            'next' => 'fillBillingAddress'
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
     * Preparing data for test.
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
     * Run CreateGiftMessageOnBackend test.
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
     * Disable enabled config after test.
     *
     * @return void
     */
    public function tearDown()
    {
        if ($this->configuration !== '-') {
            $this->setupConfiguration(true);
        }
    }

    /**
     * Setup configuration
     *
     * @param bool $rollback
     * @return void
     */
    protected function setupConfiguration($rollback = false)
    {
        $setConfigStep = $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => $this->configuration, 'rollback' => $rollback]
        );
        $setConfigStep->run();
    }
}
