<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\TestCase;

use Magento\Sales\Test\Fixture\OrderInjectable;
use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for CreateShipment for offline payment methods
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable payment method "Check/Money Order"
 * 2. Enable shipping method one of "Flat Rate/Free Shipping"
 * 3. Create order
 *
 * Steps:
 * 1. Go to Sales > Orders
 * 2. Select created order in the grid and open it
 * 3. Click 'Ship' button
 * 4. Fill data according to dataSet
 * 5. Click 'Submit Shipment' button
 * 6. Perform all asserts
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-28708
 */
class CreateShipmentEntityTest extends Injectable
{
    /**
     * Set up configuration
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public function __prepare(ObjectManager $objectManager)
    {
        $setupConfigurationStep = $objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => "checkmo,flatrate"]
        );
        $setupConfigurationStep->run();
    }

    /**
     * Create shipment
     *
     * @param ObjectManager $objectManager
     * @param OrderInjectable $order
     * @param array $data
     * @return array
     */
    public function test(ObjectManager $objectManager, OrderInjectable $order, array $data)
    {
        // Preconditions
        $order->persist();

        // Steps
        $createShipping = $objectManager->create(
            'Magento\Sales\Test\TestStep\CreateShipmentStep',
            ['order' => $order, 'data' => $data]
        );

        return ['ids' => $createShipping->run()];
    }
}
