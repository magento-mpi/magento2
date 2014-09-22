<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;

/**
 * Test Creation for MassOrdersUpdate
 *
 * Test Flow:
 *
 * Precondition:
 * 1. Create orders
 *
 * Steps:
 * 1. Navigate to backend
 * 2. Go to Sales -> Orders
 * 3. Select Mass Action according to dataSet
 * 4. Submit
 * 5. Perform Asserts
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-27897
 */
class MassOrdersUpdateTest extends Injectable
{
    /**
     * Object Manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Order index page
     *
     * @var OrderIndex
     */
    protected $orderIndex;

    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Injection data
     *
     * @param OrderIndex $orderIndex
     * @param FixtureFactory $fixtureFactory
     * @param ObjectManager $objectManager
     * @return void
     */
    public function __inject(OrderIndex $orderIndex, FixtureFactory $fixtureFactory, ObjectManager $objectManager)
    {
        $this->orderIndex = $orderIndex;
        $this->fixtureFactory = $fixtureFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * Mass orders update
     *
     * @param string $ordersStatus
     * @param int $ordersCount
     * @param string $action
     * @param string $resultStatuses
     * @return array
     */
    public function test($ordersStatus, $ordersCount, $action, $resultStatuses)
    {
        // Preconditions
        $orders = $this->createOrders($ordersCount, $ordersStatus);
        $items = [];
        foreach ($orders as $order) {
            $items[] = ['id' => $order->getId()];
        }

        // Steps
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->massaction($items, $action);

        // Prepare data for asserts
        $resultStatuses = explode(',', $resultStatuses);

        return ['orders' => $orders, 'statuses' => $resultStatuses];
    }

    /**
     * Create orders
     *
     * @param int $count
     * @param string $statuses
     * @return array
     */
    protected function createOrders($count, $statuses)
    {
        $orders = [];
        $statuses = explode(',', $statuses);
        for ($i = 0; $i < $count; $i++) {
            /** @var OrderInjectable $order */
            $order = $this->fixtureFactory->createByCode('orderInjectable', ['dataSet' => 'default']);
            $order->persist();
            $orders[$i] = $order;

            switch ($statuses[$i]) {
                case 'Closed':
                    $this->orderCreditMemo($order);
                    break;
                case 'Complete':
                    $this->orderInvoice($order, ['do_shipment' => 'Yes']);
                    break;
                case 'Processing':
                    $this->orderInvoice($order);
                    break;
                case 'On Hold':
                    $this->orderHold($order);
                    break;
            }
        }

        return $orders;
    }

    /**
     * Order invoice
     *
     * @param OrderInjectable $order
     * @param array|null $data [optional]
     * @return void
     */
    protected function orderInvoice(OrderInjectable $order, array $data = null)
    {
        $createInvoiceStep = $this->objectManager->create(
            'Magento\Sales\Test\TestStep\CreateInvoice',
            ['order' => $order, 'data' => $data]
        );
        $createInvoiceStep->run();
    }

    /**
     * Order credit memo
     *
     * @param OrderInjectable $order
     * @return void
     */
    protected function orderCreditMemo(OrderInjectable $order)
    {
        $this->orderInvoice($order);
        $createCreditMemoStep = $this->objectManager->create(
            'Magento\Sales\Test\TestStep\CreateCreditMemo',
            ['order' => $order]
        );
        $createCreditMemoStep->run();
    }

    /**
     * Order hold
     *
     * @param OrderInjectable $order
     * @return void
     */
    protected function orderHold(OrderInjectable $order)
    {
        $createOnHoldStep = $this->objectManager->create('Magento\Sales\Test\TestStep\OnHoldStep', ['order' => $order]);
        $createOnHoldStep->run();
    }
}
