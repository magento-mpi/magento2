<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;

/**
 * Class AssertOrdersInOrdersGrid
 * Assert that orders are present in Orders grid
 */
class AssertOrdersInOrdersGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that orders are present in Sales -> Orders Grid
     *
     * @param OrderInjectable[] $orders
     * @param OrderIndex $orderIndex
     * @param array $statuses
     * @param AssertOrderInOrdersGrid $assertOrderInOrdersGrid
     * @return void
     */
    public function processAssert(
        $orders,
        OrderIndex $orderIndex,
        array $statuses,
        AssertOrderInOrdersGrid $assertOrderInOrdersGrid
    ) {
        $orderIndex->open();
        foreach ($orders as $key => $order) {
             $assertOrderInOrdersGrid->assert($order, $orderIndex, $statuses[$key]);
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'All orders are present in sales orders grid.';
    }
}
