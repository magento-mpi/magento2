<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\Constraint;

use Magento\Sales\Test\Fixture\OrderStatus;
use Magento\Sales\Test\Page\Adminhtml\OrderStatusIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertOrderStatusNotAssigned
 * Assert that order status with status code from fixture have empty "State Code and Title" value
 */
class AssertOrderStatusNotAssigned extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that order status with status code from fixture have empty "State Code and Title" value
     *
     * @param OrderStatus $orderStatus
     * @param OrderStatusIndex $orderStatusIndex
     * @return void
     */
    public function processAssert(OrderStatus $orderStatus, OrderStatusIndex $orderStatusIndex)
    {
        $statusLabel = $orderStatus->getLabel();
        \PHPUnit_Framework_Assert::assertFalse(
            $orderStatusIndex->open()->getOrderStatusGrid()->isRowVisible(
                ['label' => $statusLabel, 'state' => $orderStatus->getState()]
            ),
            "Order status $statusLabel is assigned to state."
        );
    }

    /**
     * Return string representation of object
     *
     * @return string
     */
    public function toString()
    {
        return 'Order status with status code from fixture have empty "State Code and Title" value.';
    }
}
