<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveOrders;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertArchiveOrdersInGrid
 * Assert that archived orders with fixture data is in the Grid
 */
class AssertArchiveOrdersInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that orders with specified id and status is in archive orders grid
     *
     * @param ArchiveOrders $archiveOrders
     * @param AssertArchiveOrderInGrid $assert
     * @param array $orders
     * @param string $orderStatus
     * @return void
     */
    public function processAssert(
        ArchiveOrders $archiveOrders,
        AssertArchiveOrderInGrid $assert,
        array $orders,
        $orderStatus
    ) {
        $orderStatuses = explode(',', $orderStatus);
        foreach ($orders as $key => $order) {
            $assert->processAssert($order, $archiveOrders, trim($orderStatuses[$key]));
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'All orders are present in archived sales orders grid.';
    }
}
