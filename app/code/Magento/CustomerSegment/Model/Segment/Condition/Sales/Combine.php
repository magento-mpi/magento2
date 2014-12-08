<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Sales;

/**
 * Sales conditions combine
 */
class Combine extends \Magento\CustomerSegment\Model\Condition\Combine\AbstractCombine
{
    /**
     * @var string
     */
    protected $_inputType = 'numeric';

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
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Sales\Combine');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array_merge_recursive(
            parent::getNewChildSelectOptions(),
            [
                $this->_conditionFactory->create('Order\Status')->getNewChildSelectOptions(),
                // date ranges
                [
                    'value' => [
                        $this->_conditionFactory->create('Uptodate')->getNewChildSelectOptions(),
                        $this->_conditionFactory->create('Daterange')->getNewChildSelectOptions(),
                    ],
                    'label' => __('Date Ranges')
                ]
            ]
        );
    }

    /**
     * Init attribute select options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(['total' => __('Total'), 'average' => __('Average')]);
        return $this;
    }

    /**
     * Get input type for attribute value.
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Check if validation should be strict
     *
     * @return bool
     */
    protected function _getRequiredValidation()
    {
        return true;
    }

    /**
     * Get field names map for subfilters
     *
     * @return array
     */
    protected function _getSubfilterMap()
    {
        return ['order' => 'sales_order.status', 'date' => 'sales_order.created_at'];
    }
}
