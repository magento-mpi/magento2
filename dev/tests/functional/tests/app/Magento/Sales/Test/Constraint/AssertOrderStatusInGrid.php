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
 * @package Magento\Sales\Test\Constraint
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
            'Order status \'' . $orderStatus->getStatus() . '\' is absent in Order status grid.'
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'Order status is present in grid';
    }
}
