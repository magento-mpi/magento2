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
 * Customer conditions options group
 */
namespace Magento\CustomerSegment\Model\Segment\Condition;

class Customer
    extends \Magento\CustomerSegment\Model\Condition\AbstractCondition
{
    /**
     * @var \Magento\CustomerSegment\Model\ConditionFactory
     */
    protected $_conditionFactory;

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
        $this->_conditionFactory = $conditionFactory;
        parent::__construct($resourceSegment, $context, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Customer');
        $this->setValue(null);
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = $this->_conditionFactory->create('Customer\Attributes')->getNewChildSelectOptions();
        $conditions = array_merge($conditions,
            $this->_conditionFactory->create('Customer\Newsletter')->getNewChildSelectOptions());
        $conditions = array_merge($conditions,
            $this->_conditionFactory->create('Customer\Storecredit')->getNewChildSelectOptions());
        return array(
            'value' => $conditions,
            'label' => __('Customer'),
        );
    }
}
