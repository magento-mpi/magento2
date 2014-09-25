<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\TestCase;

use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;

/**
 * Test Creation for ViewingInvoicesSalesArchiveEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable "Orders Archiving" go to: Admin > Stores > Configuration > Sales > Sales > Orders, Invoices, Shipments,
 * Credit Memos Archiving (For all statuses)
 * 2. Enable payment method "Check/Money Order"
 * 3. Enable shipping method Flat Rate
 * 4. Create a product
 * 5. Place order

 * Steps:
 * 1. Create Invoice
 * 2. Move order to Archive
 * 3. Perform all assertions
 *
 * @group Sales_Archive_(CS)
 * @ZephyrId MAGETWO-28822
 */
class ViewingInvoicesSalesArchiveEntity extends Injectable
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Orders Page
     *
     * @var OrderIndex
     */
    protected $orderIndex;

    /**
     * Prepare data:
     * 1. Enable "Orders Archiving" for all statuses
     * 2. Enable payment method "Check/Money Order"
     * 3. Enable shipping method Flat Rate
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public function __prepare(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;

        $setConfigStep = $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'salesarchive_all_statuses, checkmo, flatrate']
        );
        $setConfigStep->run();
    }

    /**
     * Injection data
     *
     * @param OrderIndex $orderIndex
     * @return void
     */
    public function __inject(OrderIndex $orderIndex)
    {
        $this->orderIndex = $orderIndex;
    }

    /**
     * Test run viewing invoices sales archive entity
     *
     * @param OrderInjectable $order
     * @return array
     */
    public function test(OrderInjectable $order)
    {
        // Preconditions
        $order->persist();

        // Steps
        $createInvoice = $this->objectManager->create(
            'Magento\Sales\Test\TestStep\CreateInvoiceStep',
            ['order' => $order]
        );
        $ids = $createInvoice->run();

        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->massaction([['id' => $order->getId()]], 'Move to Archive');

        return ['ids' => $ids];
    }
}
