<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;

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
     * @param OrderIndex $salesOrder
     * @param OrderView $salesOrderView
     * @param string|null $statusToCheck
     * @return void
     */
    public function processAssert(
        $orderStatus,
        $orderId,
        OrderIndex $salesOrder,
        OrderView $salesOrderView,
        $statusToCheck = null
    ) {
        $salesOrder->open();
        $salesOrder->getSalesOrderGrid()->searchAndOpen(['id' => $orderId]);
        $status = $statusToCheck == null ? $orderStatus : $statusToCheck;

        \PHPUnit_Framework_Assert::assertEquals(
            $salesOrderView->getOrderForm()->getOrderInfoBlock()->getOrderStatus(),
            $status
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Order status is correct.';
    }
}
