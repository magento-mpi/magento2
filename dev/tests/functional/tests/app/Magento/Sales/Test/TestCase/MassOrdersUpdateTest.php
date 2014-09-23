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
        $items = $this->prepareFilter($orders);

        // Steps
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->massaction($items, $action);

        return ['orders' => $orders, 'statuses' => explode(',', $resultStatuses)];
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
                    $this->processStep('CreateInvoice', ['order' => $order]);
                    $this->processStep('CreateCreditMemo', ['order' => $order]);
                    break;
                case 'Complete':
                    $this->processStep('CreateInvoice', ['order' => $order, 'data' => ['do_shipment' => 'Yes']]);
                    break;
                case 'Processing':
                    $this->processStep('CreateInvoice', ['order' => $order]);
                    break;
                case 'On Hold':
                    $this->processStep('OnHold', ['order' => $order]);
                    break;
            }
        }

        return $orders;
    }

    /**
     * Process which step to take for order
     *
     * @param string $type
     * @param array $arguments
     * @return void
     */
    protected function processStep($type, array $arguments = [])
    {
        $processStep = $this->objectManager->create('Magento\Sales\Test\TestStep\\' . $type . 'Step', $arguments);
        $processStep->run();
    }

    /**
     * Prepare filter
     *
     * @param OrderInjectable[] $orders
     * @return array
     */
    protected function prepareFilter(array $orders)
    {
        $items = [];
        foreach ($orders as $order) {
            $items[] = ['id' => $order->getId()];
        }

        return $items;
    }
}
