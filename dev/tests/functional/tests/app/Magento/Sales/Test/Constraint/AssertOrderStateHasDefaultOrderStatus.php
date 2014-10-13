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
 * Class AssertOrderStateHasDefaultOrderStatus
 * Assert that order state has default order status after unassigning
 */
class AssertOrderStateHasDefaultOrderStatus extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Mapping data for assert
     *
     * @var array
     */
    protected $mappingData = ['Pending' => 'new'];

    /**
     * Assert that order state has default order status after unassigning
     *
     * @param string $defaultState
     * @param OrderStatusIndex $orderStatusIndexPage
     * @return void
     */
    public function processAssert($defaultState, OrderStatusIndex $orderStatusIndexPage)
    {
        $expectedtState = $this->prepareData($defaultState);
        $orderStatusIndexPage->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $orderStatusIndexPage->getOrderStatusGrid()->isRowVisible(['state' => $expectedtState], false),
            'Order state \'' . $defaultState . '\' has not default order status'
        );
    }

    /**
     * Preparing data for assert
     *
     * @param string $defaultState
     * @return string
     */
    protected function prepareData($defaultState)
    {
        foreach ($this->mappingData as $key => $data) {
            if ($defaultState == $key) {
                return $data . "[$defaultState]";
            }
        }
        return $defaultState;
    }

    /**
     * Text of Order Status in grid assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Order state has default order status.';
    }
}
