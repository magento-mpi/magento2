<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveOrders;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertArchiveOrderNotInGrid
 * Assert that order is absent in archive orders grid
 */
class AssertArchiveOrderNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that order is absent in archive orders grid
     *
     * @param OrderInjectable $order
     * @param ArchiveOrders $archiveOrders
     * @param string $orderStatus
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
        \PHPUnit_Framework_Assert::assertFalse(
            $archiveOrders->getSalesOrderGrid()->isRowVisible($filter),
            'Order with following data \'' . $errorMessage . '\' is present in archive orders grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Order is absent in archive orders grid.';
    }
}
