<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveOrders;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertArchiveOrderInGrid
 * Assert that order with fixture data is in the Grid
 */
class AssertArchiveOrderInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that order with fixture data is in archive orders grid
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
        return 'Order is present in archive orders grid.';
    }
}
