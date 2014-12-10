<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Order\Address;

use Magento\CustomerSegment\Model\Condition\Combine\AbstractCombine;

/**
 * Order address attribute conditions combine
 */
class Combine extends AbstractCombine
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        array $data = []
    ) {
        parent::__construct($context, $conditionFactory, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Order\Address\Combine');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $result = array_merge_recursive(
            parent::getNewChildSelectOptions(),
            [
                ['value' => $this->getType(), 'label' => __('Conditions Combination')],
                $this->_conditionFactory->create('Order\Address\Type')->getNewChildSelectOptions(),
                $this->_conditionFactory->create('Order\Address\Attributes')->getNewChildSelectOptions()
            ]
        );
        return $result;
    }
}
