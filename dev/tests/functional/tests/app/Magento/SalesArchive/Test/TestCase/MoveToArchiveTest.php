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
use Mtf\Fixture\FixtureFactory;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;

/**
 * Test Creation for MoveToArchive
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable "Orders Archiving" in configuration
 * 2. Enable payment method "Check/Money Order"
 * 3. Enable shipping method Flat Rate
 * 4. Create a product
 * 5. Create a customer
 * 6. Place order (and do actions according to dataset - invoice, shipment, credit memo)
 *
 * Steps:
 * 1. Go to Admin > Sales > Orders
 * 2. Select placed orders and in the 'Actions' drop-down select 'Move to Archive' option
 * 3. Click 'Submit' button
 * 4. Perform all assertions
 *
 * @group Sales_Archive_(CS)
 * @ZephyrId MAGETWO-28235
 */
class MoveToArchiveTest extends Injectable
{
    /**
     * Orders Page
     *
     * @var OrderIndex
     */
    protected $orderIndex;

    /**
     * Fixture Factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Enable Check/Money Order", "Flat Rate" in configuration
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $configPayment = $fixtureFactory->createByCode('configData', ['dataSet' => 'checkmo']);
        $configPayment->persist();

        $configShipping = $fixtureFactory->createByCode('configData', ['dataSet' => 'flatrate']);
        $configShipping->persist();
    }

    /**
     * Injection data
     *
     * @param OrderIndex $orderIndex
     * @param FixtureFactory $fixtureFactory
     * @param ObjectManager $objectManager
     * @return void
     */
    public function __inject(
        OrderIndex $orderIndex,
        FixtureFactory $fixtureFactory,
        ObjectManager $objectManager
    ) {
        $this->orderIndex = $orderIndex;
        $this->fixtureFactory = $fixtureFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * Move order to archive
     *
     * @param OrderInjectable $order
     * @param string $steps
     * @param string $configArchive
     * @return array
     */
    public function test(OrderInjectable $order, $steps, $configArchive)
    {
        // Preconditions
        $configPayment = $this->fixtureFactory->createByCode('configData', ['dataSet' => $configArchive]);
        $configPayment->persist();

        $order->persist();

        // Steps
        $this->orderIndex->open();
        $ids = $this->processSteps($order, $steps);
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->massaction([['id' => $order->getId()]], 'Move to Archive');

        return ['ids' => $ids];
    }

    /**
     * Process which step to take for order
     *
     * @param OrderInjectable $order
     * @param string $steps
     * @throws \Exception
     * @return array
     */
    protected function processSteps(OrderInjectable $order, $steps)
    {
        $steps = array_diff(explode(',', $steps), ['-']);
        $ids = [];
        foreach ($steps as $step) {
            $action = str_replace(' ', '', ucwords($step));
            $methodAction = 'Create' . $action;
            $path = 'Magento\Sales\Test\TestStep';
            $processStep = $this->objectManager->create($path . '\\' . $methodAction, ['order' => $order]);
            $ids = array_replace($ids, $processStep->run());
        }

        return $ids;
    }
}
