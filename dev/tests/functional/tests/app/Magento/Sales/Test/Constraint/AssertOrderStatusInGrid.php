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
     * Order status state data mapping
     *
     * @var array
     */
    protected $stateMapping = ["Pending" => "new"];

    /**
     * Assert order status availability in Order Status grid
     *
     * @param OrderStatus $orderStatus
     * @param OrderStatusIndex $orderStatusIndexPage
     * @param string $defaultState
     * @return void
     */
    public function processAssert(
        OrderStatus $orderStatus,
        OrderStatusIndex $orderStatusIndexPage,
        $defaultState = null
    ) {
        $orderStatusIndexPage->open();
        $orderStatusLabel = $orderStatus->getLabel();
        $filter = ['label' => $orderStatusLabel];
        if ($defaultState !== null) {
            $state = $this->prepareState($orderStatus->getState());
            $filter = ['label' => $defaultState, 'state' => $state];
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $orderStatusIndexPage->getOrderStatusGrid()->isRowVisible($filter, true, false),
            'Order status \'' . $orderStatusLabel . '\' is absent in Order Status grid.'
        );
    }

    /**
     * Prepare state value for assert
     *
     * @param string $state
     * @return string
     */
    protected function prepareState($state)
    {
        if (isset($this->stateMapping[$state])) {
            return $this->stateMapping[$state];
        } else {
            return $state;
        }
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
