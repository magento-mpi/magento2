<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\TestCase;

use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveOrders;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for OrderMassActionsSalesArchiveEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable "Orders Archiving"
 * 2. Enable payment method "Check/Money Order"
 * 3. Enable shipping method Flat Rate
 * 4. Create a product
 * 5. Create a customer
 * 6. Place orders (and do actions according to dataSet - invoice, shipment)
 * 7. Move orders to Archive
 *
 * Steps:
 * 1. Go to Admin > Sales > Archive >Orders
 * 2. Select orders and in the 'Actions' drop-down select action according to dataSet
 * 3. Click 'Submit' button
 * 4. Perform all assertions
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-28873
 */
class OrderMassActionsSalesArchiveEntityTest extends Injectable
{
    /**
     * Orders Page
     *
     * @var OrderIndex
     */
    protected $orderIndex;

    /**
     * Archive Orders Page
     *
     * @var ArchiveOrders
     */
    protected $archiveOrders;

    /**
     * Factory for fixtures
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Enable Check/Money Order", "Flat Rate" in configuration
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
        $configPayment = $fixtureFactory->createByCode('configData', ['dataSet' => 'checkmo']);
        $configPayment->persist();
        $configShipping = $fixtureFactory->createByCode('configData', ['dataSet' => 'flatrate']);
        $configShipping->persist();
        $configPayment = $fixtureFactory->createByCode('configData', ['dataSet' => 'salesarchive_all_statuses']);
        $configPayment->persist();
    }

    /**
     * Injection data
     *
     * @param OrderIndex $orderIndex
     * @param ArchiveOrders $archiveOrders
     * @return void
     */
    public function __inject(OrderIndex $orderIndex, ArchiveOrders $archiveOrders)
    {
        $this->orderIndex = $orderIndex;
        $this->archiveOrders = $archiveOrders;
    }

    /**
     * Move order to archive
     *
     * @param string $steps
     * @param int $ordersQty
     * @param string $massAction
     * @return array
     */
    public function test($steps, $ordersQty, $massAction)
    {
        // Preconditions
        $orders = [];
        $ordersIds = [];
        for (; $ordersQty > 0; $ordersQty--) {
            /** @var OrderInjectable $order */
            $order = $this->fixtureFactory->createByCode('orderInjectable');
            $order->persist();
            $orders[] = $order;
            $ordersIds[] = ['id' => $order->getId()];
        }

        // Steps
        $steps = explode(';', $steps);
        $this->orderIndex->open();
        foreach ($orders as $key => $order) {
            $this->processSteps($order, trim($steps[$key]));
        }
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->massaction($ordersIds, 'Move to Archive');
        $this->archiveOrders->open();
        $this->archiveOrders->getSalesOrderGrid()->massaction($ordersIds, $massAction);

        return ['orders' => $orders];
    }

    /**
     * Process which step to take for order
     *
     * @param OrderInjectable $order
     * @param string $steps
     * @return array
     */
    protected function processSteps(OrderInjectable $order, $steps)
    {
        $steps = array_diff(explode(',', $steps), ['-']);
        foreach ($steps as $step) {
            $action = str_replace(' ', '', ucwords($step));
            $methodAction = 'Create' . $action . 'Step';
            $path = 'Magento\Sales\Test\TestStep';
            $processStep = $this->objectManager->create($path . '\\' . $methodAction, ['order' => $order]);
            $processStep->run();
        }
    }
}
