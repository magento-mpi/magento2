<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Test\Constraint;

use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Class AssertArchiveOrderInOrderGrid
 */
class AssertArchiveOrderInOrderGrid extends AbstractConstraint
{
    /**
     * Constant severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that status is correct on order page in backend (same with value of orderStatus variable)
     *
     * @param OrderInjectable $order
     * @param OrderView $orderView
     * @param OrderIndex $salesOrder
     * @param string $orderStatus
     * @return void
     */
    public function processAssert(
        OrderInjectable $order,
        OrderView $orderView,
        OrderIndex $salesOrder,
        $orderStatus
    ) {
        $filter = [
            'id' => $order->getId(),
            'status' => $orderStatus,
        ];
        $salesOrder->open();
        $salesOrder->getSalesOrderGrid()->searchAndOpen($filter);
        $actualOrderStatus = $orderView->getOrderInfoBlock()->getOrderStatus();
        \PHPUnit_Framework_Assert::assertEquals(
            $orderStatus,
            $actualOrderStatus,
            "Order status is not correct on archive orders page backend."
        );
    }
    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Order status is correct on orders page backend.';
    }
}
