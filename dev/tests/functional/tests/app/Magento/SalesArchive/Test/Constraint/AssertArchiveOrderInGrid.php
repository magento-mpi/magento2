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
 * Class AssertArchiveOrderInGrid
 * Assert that order with fixture data is in the Grid
 */
class AssertArchiveOrderInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

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
