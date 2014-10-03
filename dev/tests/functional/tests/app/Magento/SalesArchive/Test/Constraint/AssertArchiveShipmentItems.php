<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\Sales\Test\Page\Adminhtml\OrderShipmentView;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveShipments;

/**
 * Class AssertArchiveShipmentItems
 * Assert shipped products are represented on archived shipment page
 */
class AssertArchiveShipmentItems extends AbstractAssertArchiveItems
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
        $productsData = $this->prepareOrderProducts($order);
        foreach ($ids['shipmentIds'] as $shipmentId) {
            $filter = [
                'order_id' => $orderId,
                'shipment_id' => $shipmentId
            ];

            $archiveShipments->open();
            $archiveShipments->getShipmentsGrid()->searchAndOpen($filter);
            $itemsData = $this->preparePageItems($orderShipmentView->getItemsBlock()->getData());
            $error = $this->verifyData($productsData, $itemsData);
            \PHPUnit_Framework_Assert::assertEmpty($error, $error);
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'All shipment products are present in archived shipment page.';
    }
}
