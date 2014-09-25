<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\TestCase;

use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Test Creation for ViewShipmentsSalesArchiveEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable "Orders Archiving" in configuration
 * 2. Enable payment method "Check/Money Order"
 * 3. Enable shipping method Flat Rate
 * 4. Create a product
 * 5. Create a customer
 * 6. Place order with invoice
 * 7. Move order to Archive
 *
 * Steps:
 * 1. Go to Admin > Sales > Archive > Orders
 * 2. Create Shipment
 * 3. Go to Admin > Sales > Archive > Shipments
 * 4. Spen created shipment
 * 5. Perform all assertions
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-28870
 */
class ViewShipmentsSalesArchiveEntityTest extends MoveToArchiveTest
{
    /**
     * View Shipments Sales Archive
     *
     * @param OrderInjectable $order
     * @param string $steps
     * @param string $configArchive
     * @return array
     */
    public function test(OrderInjectable $order, $steps, $configArchive)
    {
        // Preconditions:
        parent::test($order, $steps, $configArchive);
        // Steps:
        return $this->processSteps($order, 'ArchiveShipping');
    }
}
