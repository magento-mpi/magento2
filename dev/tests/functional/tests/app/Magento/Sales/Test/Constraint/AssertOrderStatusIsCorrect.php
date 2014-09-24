<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Magento\Sales\Test\Page\SalesOrder;
use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\Adminhtml\OrderView;

/**
 * Class AssertOrderStatusIsCorrect
 * Assert that status is correct on order page in backend (same with value of orderStatus variable)
 */
class AssertOrderStatusIsCorrect extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that status is correct on order page in backend (same with value of orderStatus variable)
     *
     * @param string $orderStatus
     * @param string $orderId
     * @param SalesOrder $salesOrder
     * @param OrderView $salesOrderView
     * @return void
     */
    public function processAssert(
        $orderStatus,
        $orderId,
        SalesOrder $salesOrder,
        OrderView $salesOrderView
    ) {
        $salesOrder->open();
        $salesOrder->getOrderGridBlock()->searchAndOpen(['id' => $orderId]);
        $actualOrderStatus = $salesOrderView->getOrderInfoBlock()->getOrderStatus();

        \PHPUnit_Framework_Assert::assertEquals(
            $actualOrderStatus,
            $orderStatus,
            'Wrong order status is displayed.'
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
