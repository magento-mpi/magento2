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
 * Class AssertOrderInOrdersGrid
 * Assert that order with fixure data is present in Sales -> Orders Grid
 */
class AssertOrderInOrdersGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that order with fixure data is present in Sales -> Orders Grid
     *
     * @param OrderInjectable $order
     * @param OrderIndex $orderIndex
     * @param string $status
     * @return void
     */
    public function processAssert(OrderInjectable $order, OrderIndex $orderIndex, $status)
    {
        $data = $order->getData();
        $filter = [
            'id' => $data['id'],
            'status' => $status,
        ];
        $orderIndex->open();
        $errorMessage = implode(', ', $filter);
        \PHPUnit_Framework_Assert::assertTrue(
            $orderIndex->getSalesOrderGrid()->isRowVisible($filter),
            'Order with following data \'' . $errorMessage . '\' is absent in Orders grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Sales order is present in sales orders grid.';
    }
}
