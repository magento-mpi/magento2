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
 * Class AssertArchiveShipmentItems
 * Assert shipped products are represented on archived shipment page
 */
class AssertArchiveShipmentItems extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert shipped products are represented on archived shipment page
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

        foreach ($ids['shipmentIds'] as $shipmentId) {
            $archiveShipments->open();
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
                    'Shipped product ' . $productName . ' is absent on archived shipment page.'
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
        return 'All shipment products are present in archived shipment grid.';
    }
}
