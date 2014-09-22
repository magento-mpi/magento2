<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;

/**
 * Test Creation for CancelCreatedOrder
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable payment method "Check/Money Order"
 * 2. Enable shipping method one of "Flat Rate"
 * 3. Create order
 *
 * Steps:
 * 1. Login to backend
 * 2. Sales > Orders
 * 3. Open the created order
 * 4. Do cancel Order
 * 5. Perform all assetions
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-28191
 */
class CancelCreatedOrderTest extends Injectable
{
    /**
     * Orders Page
     *
     * @var OrderIndex
     */
    protected $orderIndex;

    /**
     * Order View Page
     *
     * @var OrderView
     */
    protected $orderView;

    /**
     * Enable "Check/Money Order" and "Flat Rate" in configuration
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
     * @param OrderView $orderView
     * @return void
     */
    public function __inject(OrderIndex $orderIndex, OrderView $orderView)
    {
        $this->orderIndex = $orderIndex;
        $this->orderView = $orderView;
    }

    /**
     * Cancel created order
     *
     * @param OrderInjectable $order
     * @param string $status
     * @return array
     */
    public function test(OrderInjectable $order, $status)
    {
        // Preconditions
        $order->persist();

        // Steps
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $order->getId()]);
        $this->orderView->getPageActions()->cancel();

        return [
            'customer' => $order->getDataFieldConfig('customer_id')['source']->getCustomer(),
            'orders' => [$order],
            'statuses' => [$status]
        ];
    }
}
