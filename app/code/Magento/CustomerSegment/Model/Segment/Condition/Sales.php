<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Orders conditions options group
 */
namespace Magento\CustomerSegment\Model\Segment\Condition;

class Sales extends \Magento\CustomerSegment\Model\Condition\AbstractCondition
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        array $data = []
    ) {
        parent::__construct($context, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Sales');
        $this->setValue(null);
    }

    /**
     * Get condition "selectors"
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return [
            'value' => [
                [
                     // order address combo
                    'value' => 'Magento\CustomerSegment\Model\Segment\Condition\Order\Address',
                    'label' => __('Order Address'),
                ],
                [
                    'value' => 'Magento\CustomerSegment\Model\Segment\Condition\Sales\Salesamount',
                    'label' => __('Sales Amount')
                ],
                [
                    'value' => 'Magento\CustomerSegment\Model\Segment\Condition\Sales\Ordersnumber',
                    'label' => __('Number of Orders')
                ],
                [
                    'value' => 'Magento\CustomerSegment\Model\Segment\Condition\Sales\Purchasedquantity',
                    'label' => __('Purchased Quantity')
                ],
            ],
            'label' => __('Sales')
        ];
    }
}
