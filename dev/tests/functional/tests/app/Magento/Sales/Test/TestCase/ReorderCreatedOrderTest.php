<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\TestCase\Scenario;
use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Test Creation for ReorderCreatedOrder
 *
 * Test Flow:
 * Precondtions:
 * 1. Enable payment method "Check/Money Order"
 * 2. Enable shipping method one of "Flat Rate"
 * 3. Create two products
 * 4. Create a customer
 * 5. Create order
 *
 * Steps:
 * 1. Go to Admin
 * 2. Sales > Orders
 * 3. Open the created order
 * 4. Do 'Reorder' for placed order
 * 5. Perform all assertions
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-29007
 */
class ReorderCreatedOrderTest extends Scenario
{
    /**
     * Configuration data set name
     *
     * @var string
     */
    protected $configuration;

    /**
     * Steps for scenario
     *
     * @var array
     */
    protected $scenario = [
        'ReorderCreatedOrderTest' => [
            'methods' => [
                'test' => [
                    'scenario' => [
                        'createSalesRule' => [
                            'module' => 'Magento_SalesRule',
                            'next' => 'openOrder'
                        ],
                        'openOrder' => [
                            'module' => 'Magento_Sales',
                            'next' => 'clickReorder'
                        ],
                        'clickReorder' => [
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
     * Reorder created product
     *
     * @param OrderInjectable $order
     * @param string $config
     * @return void
     */
    public function test(OrderInjectable $order, $config)
    {
        $this->configuration = $config;
        $order->persist();
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
