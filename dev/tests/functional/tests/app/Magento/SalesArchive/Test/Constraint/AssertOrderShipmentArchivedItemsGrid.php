<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\Sales\Test\Page\Adminhtml\OrderShipmentView;
use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveShipments;

/**
 * Class AssertOrderShipmentArchivedItemsGrid
 * Assert shipped product represented on archived shipment page
 */
class AssertOrderShipmentArchivedItemsGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert shipped product represented on archived shipment page
     *
     * @param ArchiveShipments $archiveShipments
     * @param OrderShipmentView $orderShipmentView
     * @param OrderInjectable $order
     * @param array $ids
     * @return void
     */
    public function processAssert(
        ArchiveShipments $archiveShipments,
        OrderShipmentView $orderShipmentView,
        OrderInjectable $order,
        array $ids
    ) {
        $orderId = $order->getId();
        $archiveShipments->open();

        foreach ($ids['shipmentIds'] as $shipmentId) {
            $filter = [
                'order_id' => $orderId,
                'shipment_id' => $shipmentId
            ];
            $archiveShipments->getShipmentsGrid()->searchAndOpen($filter);

            foreach ($order->getEntityId()['products'] as $product) {
                $productName = $product->getName();
                $filter = ['name' => $productName];

                \PHPUnit_Framework_Assert::assertTrue(
                    $orderShipmentView->getProductsBlock()->isRowVisible($filter, false, false),
                    'Shipped product ' . $productName . ' is absent on archived shipment page'
                );
            }
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Shipped product is present on archived shipment page';
    }
}
