<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order address attribute conditions combine
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Order\Address;

class Combine
    extends \Magento\CustomerSegment\Model\Condition\Combine\AbstractCombine
{

    /**
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        \Magento\Rule\Model\Condition\Context $context,
        array $data = array()
    ) {
        parent::__construct($conditionFactory, $resourceSegment, $context, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Order\Address\Combine');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $result = array_merge_recursive(parent::getNewChildSelectOptions(), array(
            array(
                'value' => $this->getType(),
                'label' => __('Conditions Combination'),
            ),
            $this->_conditionFactory->create('Order\Address\Type')->getNewChildSelectOptions(),
            $this->_conditionFactory->create('Order\Address\Attributes')->getNewChildSelectOptions(),
        ));
        return $result;
    }
}
