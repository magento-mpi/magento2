<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;

/**
 * Class AssertShipmentInShipmentsTab
 * Assert that shipment is present in the Shipments tab with correct shipped items quantity
 */
class AssertShipmentInShipmentsTab extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that shipment is present in the Shipments tab with correct shipped items quantity
     *
     * @param OrderView $orderView
     * @param OrderIndex $orderIndex
     * @param OrderInjectable $order
     * @param string $shipmentId
     * @return void
     */
    public function processAssert(OrderView $orderView, OrderIndex $orderIndex, OrderInjectable $order, $shipmentId)
    {
        $orderIndex->open();
        $orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $order->getId()]);
        $orderView->getOrderForm()->openTab('shipments');
        $qty = $order->getTotalQtyOrdered();
        $filter = [
            'id' => $shipmentId,
            'qty_from' => $qty,
            'qty_to' => $qty
        ];
        \PHPUnit_Framework_Assert::assertTrue(
            $orderView->getOrderForm()->getTabElement('shipments')->getGridBlock()->isRowVisible($filter),
            'Shipment is absent on shipments tab.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Shipment is present on shipments tab.';
    }
}
