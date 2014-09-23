<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;

/**
 * Class AssertOrdersInOrdersGrid
 * Assert that orders is present in Orders grid
 */
class AssertOrdersInOrdersGrid extends AssertOrderInOrdersGrid
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that orders with fixture data is present in Sales -> Orders Grid
     *
     * @param OrderInjectable[] $orders
     * @param OrderIndex $orderIndex
     * @param string[] $statuses
     * @return void
     */
    public function processAssert($orders, OrderIndex $orderIndex, $statuses)
    {
        $orderIndex->open();
        foreach ($orders as $key => $order) {
            $this->assert($order, $orderIndex, $statuses[$key]);
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Sales orders is present in sales orders grid.';
    }
}
