<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveShipments;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertArchiveShipmentInGrid
 * Shipment with corresponding fixture data is present in Sales Archive Shipments grid
 */
class AssertArchiveShipmentInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Shipment with corresponding fixture data is present in Sales Archive Shipments grid
     *
     * @param ArchiveShipments $archiveShipments
     * @param OrderInjectable $order
     * @param array $ids
     * @return void
     */
    public function processAssert(ArchiveShipments $archiveShipments, OrderInjectable $order, array $ids)
    {
        $orderId = $order->getId();
        $archiveShipments->open();

        foreach ($ids['shipmentIds'] as $shipmentId) {
            $filter = [
                'order_id' => $orderId,
                'shipment_id' => $shipmentId,
            ];

            $errorMessage = implode(', ', $filter);
            \PHPUnit_Framework_Assert::assertTrue(
                $archiveShipments->getShipmentsGrid()->isRowVisible($filter),
                'Shipment with following data \'' . $errorMessage . '\' is absent in archive shipments grid.'
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
        return 'Shipment is present in archive shipments grid.';
    }
}
