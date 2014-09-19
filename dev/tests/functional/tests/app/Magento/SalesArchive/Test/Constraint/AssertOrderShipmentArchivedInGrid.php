<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveShipments;

/**
 * Class AssertOrderShipmentArchivedInGrid
 * Shipment with corresponding fixture data is present in Sales Archive Shipments grid
 */
class AssertOrderShipmentArchivedInGrid extends AbstractConstraint
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
        $data = $order->getData();
        $filter = [
            'shipment_id' => $ids['shippingId'],
            'order_id' => $data['id'],
        ];
        $archiveShipments->open();
        $errorMessage = implode(', ', $filter);
        \PHPUnit_Framework_Assert::assertTrue(
            $archiveShipments->getShipmentsGrid()->isRowVisible($filter),
            'Shipment with following data \'' . $errorMessage . '\' is absent in archive shipments grid.'
        );
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
