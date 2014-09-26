<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Shipping\Test\Page\Adminhtml\ShipmentIndex;

/**
 * Class AssertShipmentInShipmentsGrid
 * Assert shipment with corresponding shipment/order ID is present in 'Shipments' with correct 'Total Quantity' field
 */
class AssertShipmentInShipmentsGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert shipment with corresponding shipment/order ID is present in 'Shipments' with correct total qty field
     *
     * @param ShipmentIndex $shipmentIndex
     * @param OrderInjectable $order
     * @param array $ids
     * @return void
     */
    public function processAssert(ShipmentIndex $shipmentIndex, OrderInjectable $order, array $ids)
    {
        $shipmentIndex->open();
        $orderId = $order->getId();
        $totalQty = $order->getTotalQtyOrdered();
        foreach ($ids['shipmentIds'] as $key => $shipmentIds) {
            $filter = [
                'id' => $shipmentIds,
                'order_id' => $orderId,
                'total_qty_from' => $totalQty[$key],
                'total_qty_to' => $totalQty[$key]
            ];
            \PHPUnit_Framework_Assert::assertTrue(
                $shipmentIndex->getShipmentGrid()->isRowVisible($filter),
                'Shipment is absent in shipment grid on shipment index page.'
            );
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Shipment is present in the shipment grid with correct total qty on shipment index page.';
    }
}
