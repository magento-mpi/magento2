<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Magento\Sales\Test\Page\Adminhtml\OrderStatusIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertOrderStatusInGrid
 * Assert that order status is visible in order status grid on backend
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
     * @param string $orderStatus
     * @param OrderStatusIndex $orderStatusIndexPage
     * @return void
     */
    public function processAssert(
        $orderStatus,
        OrderStatusIndex $orderStatusIndexPage
    ) {
        $orderStatusIndexPage->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $orderStatusIndexPage->getOrderStatusGrid()->isRowVisible(['label' => $orderStatus]),
            'Order status \'' . $orderStatus . '\' is absent in Order Status grid.'
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
