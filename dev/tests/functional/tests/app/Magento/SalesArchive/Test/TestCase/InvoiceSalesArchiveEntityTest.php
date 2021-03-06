<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesArchive\Test\TestCase;

use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderInvoiceNew;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveOrders;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for InvoiceSalesArchiveEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable "Orders Archiving" for all statuses in configuration
 * 2. Enable payment method "Check/Money Order"
 * 3. Enable shipping method Flat Rate
 * 4. Place order with product qty = 2
 * 5. Move order to Archive
 *
 * Steps:
 * 1. Go to Admin > Sales > Archive > Orders
 * 2. Select orders and do Invoice
 * 3. Fill data from dataSet
 * 4. Click 'Submit' button
 * 5. Perform all assertions
 *
 * @group Sales_Archive_(CS)
 * @ZephyrId MAGETWO-28947
 */
class InvoiceSalesArchiveEntityTest extends Injectable
{
    /**
     * Orders page
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
     * Order view page
     *
     * @var OrderView
     */
    protected $orderView;

    /**
     * Order new invoice page
     *
     * @var OrderInvoiceNew
     */
    protected $orderInvoiceNew;

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
     * @param OrderInvoiceNew $orderInvoiceNew
     * @return void
     */
    public function __inject(
        OrderIndex $orderIndex,
        ArchiveOrders $archiveOrders,
        OrderView $orderView,
        OrderInvoiceNew $orderInvoiceNew
    ) {
        $this->orderIndex = $orderIndex;
        $this->archiveOrders = $archiveOrders;
        $this->orderView = $orderView;
        $this->orderInvoiceNew = $orderInvoiceNew;
    }

    /**
     * Create Invoice SalesArchive Entity
     *
     * @param OrderInjectable $order
     * @param array $data
     * @return array
     */
    public function test(OrderInjectable $order, array $data)
    {
        $this->markTestIncomplete('MAGETWO-30796');
        // Preconditions
        $order->persist();
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->massaction([['id' => $order->getId()]], 'Move to Archive');

        // Steps
        $this->archiveOrders->open();
        $this->archiveOrders->getSalesOrderGrid()->searchAndOpen(['id' => $order->getId()]);
        $this->orderView->getPageActions()->invoice();
        $this->orderInvoiceNew->getFormBlock()->fillData($data, $order->getEntityId()['products']);
        $this->orderInvoiceNew->getFormBlock()->submit();

        $this->orderView->getOrderForm()->openTab('invoices');

        return [
            'ids' => [
                'invoiceIds' => $this->orderView->getOrderForm()->getTabElement('invoices')->getGridBlock()->getIds(),
            ],
            'orderId' => $order->getId()
        ];
    }
}
