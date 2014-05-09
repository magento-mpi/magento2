<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint; 

use Magento\Sales\Test\Fixture\OrderStatus;
use Magento\Sales\Test\Page\Adminhtml\OrderStatusIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertOrderStatusInGrid
 *
 */
class AssertOrderStatusInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert order status availability in Order Status grid
     *
     * @param OrderStatus $orderStatus
     * @param OrderStatusIndex $orderStatusIndexPage
     * @return void
     */
    public function processAssert(
        OrderStatus $orderStatus,
        OrderStatusIndex $orderStatusIndexPage
    ) {
        $filter = [
            'status' => $orderStatus->getStatus(),
            'label' => $orderStatus->getLabel()
        ];
        $orderStatusIndexPage->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $orderStatusIndexPage->getOrderStatusGrid()->isRowVisible($filter),
            'Order status \'' . $orderStatus->getStatus() . '\' is absent in Order Status grid.'
        );
    }

    /**
     * Text of Order Status in grid assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Order status is present in grid';
    }
}
