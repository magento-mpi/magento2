<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveOrders;

/**
 * Class AssertOrderStatusInArchiveOrders
 * Assert that status is correct on order page in backend
 */
class AssertOrderStatusInArchiveOrders extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that status is correct on order page in backend (same with value of orderStatus variable)
     *
     * @param OrderInjectable $order
     * @param ArchiveOrders $archiveOrders
     * @param OrderView $orderView
     * @param string $orderStatus
     * @return void
     */
    public function processAssert(
        OrderInjectable $order,
        ArchiveOrders $archiveOrders,
        OrderView $orderView,
        $orderStatus
    ) {
        $filter = [
            'id' => $order->getId(),
            'status' => $orderStatus,
        ];
        $archiveOrders->open();
        $archiveOrders->getSalesOrderGrid()->searchAndOpen($filter);
        $actualOrderStatus = $orderView->getOrderInfoBlock()->getOrderStatus();
        \PHPUnit_Framework_Assert::assertEquals(
            $orderStatus,
            $actualOrderStatus,
            "Order status is not correct on archive orders page backend."
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Order status is correct on archive orders page backend.';
    }
}
