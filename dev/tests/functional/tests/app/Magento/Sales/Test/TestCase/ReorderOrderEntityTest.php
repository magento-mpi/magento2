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
 * Test Creation for ReorderOrderEntityTest
 *
 * Test Flow:
 * Preconditions:
 * 1. Create two products
 * 2. Create a customer
 * 3. Create order
 *
 * Steps:
 * 1. Go to backend
 * 2. Open Sales > Orders
 * 3. Open the created order
 * 4. Do 'Reorder' for placed order
 * 5. Perform all assertions
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-29007
 */
class ReorderOrderEntityTest extends Scenario
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
        'ReorderOrderEntityTest' => [
            'methods' => [
                'test' => [
                    'scenario' => [
                        'setupConfiguration' => [
                            'module' => 'Magento_Core',
                            'next' => 'createSalesRule'
                        ],
                        'createSalesRule' => [
                            'module' => 'Magento_SalesRule',
                            'next' => 'createOrder'
                        ],
                        'createOrder' => [
                            'module' => 'Magento_Sales',
                            'next' => 'openOrder'
                        ],
                        'openOrder' => [
                            'module' => 'Magento_Sales',
                            'next' => 'onReorder'
                        ],
                        'onReorder' => [
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
     * Reorder created order
     *
     * @param string $configData
     * @return void
     */
    public function test($configData)
    {
        $this->configuration = $configData;
        $this->executeScenario($this->scenario);
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
            ['configData' => $this->configuration, 'rollback' => true]
        );
        $setConfigStep->run();
    }
}
