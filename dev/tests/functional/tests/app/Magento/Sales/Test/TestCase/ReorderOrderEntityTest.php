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
     * Reorder created order
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
