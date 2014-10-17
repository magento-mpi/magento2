<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

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
    }
}
