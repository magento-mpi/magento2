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
use Mtf\Fixture\FixtureFactory;

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
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $configPayment = $fixtureFactory->createByCode('configData', ['dataSet' => 'checkmo']);
        $configPayment->persist();

        $configShipping = $fixtureFactory->createByCode('configData', ['dataSet' => 'flatrate']);
        $configShipping->persist();
    }

    /**
     * Create shipment
     *
     * @param ObjectManager $objectManager
     * @param OrderInjectable $order
     * @param array $shipment
     * @return array
     */
    public function test(ObjectManager $objectManager, OrderInjectable $order, array $shipment)
    {
        // Preconditions
        $order->persist();

        // Steps
        $createShipping = $objectManager->create(
            'Magento\Sales\Test\TestStep\CreateShipmentStep',
            ['order' => $order, 'data' => $shipment]
        );
        $data = $createShipping->run();

        return [
            'ids' => [
                'shipmentIds' => $data['shipmentIds'],
            ],
            'successMessage' => $data['successMessage'],
        ];
    }
}
