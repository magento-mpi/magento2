<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Constraint;

use Magento\Sales\Test\Page\SalesOrder;
use Magento\Sales\Test\Page\SalesOrderView;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertOrderStatus
 * Assert  that status is correct on order page in backend (same with value of orderStatus variable)
 */
class AssertOrderStatus extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert  that status is correct on order page in backend (same with value of orderStatus variable)
     *
     * @param $orderStatus
     * @param $orderId
     * @param SalesOrder $salesOrder
     * @param SalesOrderView $salesOrderView
     * @return void
     */
    public function processAssert(
        $orderStatus,
        $orderId,
        SalesOrder $salesOrder,
        SalesOrderView $salesOrderView
    ) {
        $filter = ['id' => $orderId];

        $salesOrder->open();
        $salesOrder->getOrderGridBlock()->searchAndOpen($filter);
        $actualOrderStatus = $salesOrderView->getOrderInfoBlock()->getOrderStatus();

        \PHPUnit_Framework_Assert::assertEquals(
            $actualOrderStatus,
            $orderStatus,
            'Wrong order status is displayed.'
            . "\nExpected: " . $orderStatus
            . "\nActual: " . $actualOrderStatus
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Order status is correct';
    }
}
