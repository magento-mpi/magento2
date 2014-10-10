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
 * Class AssertUnassignOrderStatusNotAssigned
 * Assert that order status with status code from fixture have empty "State Code and Title" value
 */
class AssertUnassignOrderStatusNotAssigned extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Mapping values for data
     *
     * @var array
     */
    protected $mappingData = [
        'state' => [
            'Pending' => 'new'
        ]
    ];

    /**
     * Assert that order status with status code from fixture have empty "State Code and Title" value
     *
     * @param OrderStatus $orderStatus
     * @param OrderStatusIndex $orderStatusIndex
     * @return void
     */
    public function processAssert(OrderStatus $orderStatus, OrderStatusIndex $orderStatusIndex)
    {
        $data = $this->replaceMappingData($orderStatus->getData());
        $statusLabel = $data['label'];
        \PHPUnit_Framework_Assert::assertFalse(
            $orderStatusIndex->open()->getOrderStatusGrid()->isRowVisible(
                ['label' => $statusLabel, 'state' => $data['state']]
            ),
            "Order status $statusLabel is assigned to state."
        );
    }

    /**
     * Replace mapping data in fixture data
     *
     * @param array $data
     * @return array
     */
    protected function replaceMappingData(array $data)
    {
        $suffix = "[{$data['label']}]";
        foreach ($data as $key => $value) {
            $data[$key] = isset($this->mappingData[$key][$value]) ? $this->mappingData[$key][$value] : $value;
            $data[$key] .= $suffix;
        }

        return $data;
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
