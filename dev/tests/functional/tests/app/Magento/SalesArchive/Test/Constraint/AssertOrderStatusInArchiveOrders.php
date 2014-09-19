<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveOrders;

/**
 * Class AssertOrderStatusInArchiveOrders
 * Assert  that status is correct on order page in backend
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
     * Assert  that status is correct on order page in backend (same with value of orderStatus variable)
     *
     * @param OrderInjectable $order
     * @param ArchiveOrders $archiveOrders
     * @param $orderStatus
     * @return void
     */
    public function processAssert(OrderInjectable $order, ArchiveOrders $archiveOrders, $orderStatus)
    {
        $data = $order->getData();
        $filter = [
            'id' => $data['id'],
            'status' => $orderStatus,
        ];
        $archiveOrders->open();
        $errorMessage = implode(', ', $filter);
        \PHPUnit_Framework_Assert::assertTrue(
            $archiveOrders->getSalesOrderGrid()->isRowVisible($filter),
            'Order with following data \'' . $errorMessage . '\' is absent in archive orders grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Order status is correct on order page backend.';
    }
}
