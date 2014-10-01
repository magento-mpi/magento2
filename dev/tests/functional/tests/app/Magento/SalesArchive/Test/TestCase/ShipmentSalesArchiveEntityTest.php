<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Shipping\Test\Page\Adminhtml\OrderShipmentNew;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveOrders;
use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Test Creation for ShipmentSalesArchiveEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable "Orders Archiving" for all statuses in configuration
 * 2. Enable payment method "Check/Money Order"
 * 3. Enable shipping method Flat Rate
 * 4. Place order with product qty = 2
 * 5. Invoice order with 2 products
 * 6. Move orders to Archive
 *
 * Steps:
 * 1. Go to Admin > Sales > Archive > Orders
 * 2. Select orders and do Shipment
 * 3. Fill data from dataSet
 * 4. Click 'Submit' button
 * 5. Perform all assertions
 *
 * @group Sales_Archive_(CS)
 * @ZephyrId MAGETWO-28781
 */
class ShipmentSalesArchiveEntityTest extends Injectable
{
    /**
     * Orders Page
     *
     * @var OrderIndex
     */
    protected $orderIndex;

    /**
     * Archive orders page
     *
     * @var ArchiveOrders
     */
    protected $archiveOrders;

    /**
     * Order View Page
     *
     * @var OrderView
     */
    protected $orderView;

    /**
     * New Order Shipment Page
     *
     * @var OrderShipmentNew
     */
    protected $orderShipmentNew;

    /**
     * Enable "Check/Money Order", "Flat Rate" and archiving for all statuses in configuration
     *
     * @return void
     */
    public function __prepare()
    {
        $setupConfig = $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'salesarchive_all_statuses, checkmo, flatrate']
        );
        $setupConfig->run();
    }

    /**
     * Injection data
     *
     * @param OrderIndex $orderIndex
     * @param ArchiveOrders $archiveOrders
     * @param OrderView $orderView
     * @param OrderShipmentNew $orderShipmentNew
     * @return void
     */
    public function __inject(
        OrderIndex $orderIndex,
        ArchiveOrders $archiveOrders,
        OrderView $orderView,
        OrderShipmentNew $orderShipmentNew
    ) {
        $this->orderIndex = $orderIndex;
        $this->archiveOrders = $archiveOrders;
        $this->orderView = $orderView;
        $this->orderShipmentNew = $orderShipmentNew;
    }

    /**
     * Create Shipment SalesArchive Entity
     *
     * @param OrderInjectable $order
     * @param string $invoice
     * @param array $qty
     * @return array
     */
    public function test(OrderInjectable $order, $invoice, array $qty)
    {
        $this->markTestIncomplete('MAGETWO-28872, MAGETWO-28867');
        // Preconditions
        $order->persist();
        if ($invoice) {
            $this->objectManager->create('Magento\Sales\Test\TestStep\CreateInvoiceStep', ['order' => $order])->run();
        }
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->massaction([['id' => $order->getId()]], 'Move to Archive');

        // Steps
        $createArchiveShipmentStep = $this->objectManager->create(
            'Magento\SalesArchive\Test\TestStep\CreateArchiveShipmentStep',
            ['order' => $order, 'data' => ['qty' => $qty]]
        );
        $data = $createArchiveShipmentStep->run();


        return [
            'ids' => [
                'shipmentIds' => $data['shipmentIds'],
            ],
            'successMessage' => $data['successMessage'],
        ];
    }
}
