<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveShipments;
use Magento\Shipping\Test\Constraint\AssertShipmentItems;
use Magento\Shipping\Test\Page\Adminhtml\SalesShipmentView;
use Mtf\ObjectManager;

/**
 * Class AssertArchiveShipmentItems
 * Assert shipped products are represented on archived shipment page
 */
class AssertArchiveShipmentItems extends AssertShipmentItems
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param ArchiveShipments $archiveShipments
     */
    public function __construct(ObjectManager $objectManager, ArchiveShipments $archiveShipments)
    {
        $this->objectManager = $objectManager;
        $this->shipmentPage = $archiveShipments;
    }

    /**
     * Assert shipped products are represented on archived shipment page
     *
     * @param SalesShipmentView $salesShipmentView
     * @param OrderInjectable $order
     * @param array $ids
     * @param array|null $data [optional]
     * @return void
     */
    public function processAssert(
        SalesShipmentView $salesShipmentView,
        OrderInjectable $order,
        array $ids,
        array $data = null
    ) {
        $this->shipmentPage->open();
        $this->assert($order, $ids, $salesShipmentView, $data);
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
