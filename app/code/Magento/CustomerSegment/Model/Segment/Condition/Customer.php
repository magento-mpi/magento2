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
class Magento_CustomerSegment_Model_Segment_Condition_Customer
    extends Magento_CustomerSegment_Model_Condition_Abstract
{
    /**
     * @var Magento_CustomerSegment_Model_ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @param Magento_CustomerSegment_Model_ConditionFactory $conditionFactory
     * @param Magento_CustomerSegment_Model_Resource_Segment $resourceSegment
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CustomerSegment_Model_ConditionFactory $conditionFactory,
        Magento_CustomerSegment_Model_Resource_Segment $resourceSegment,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        $this->_conditionFactory = $conditionFactory;
        parent::__construct($resourceSegment, $context, $data);
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Customer');
        $this->setValue(null);
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = $this->_conditionFactory->create('Customer_Attributes')->getNewChildSelectOptions();
        $conditions = array_merge($conditions,
            $this->_conditionFactory->create('Customer_Newsletter')->getNewChildSelectOptions());
        $conditions = array_merge($conditions,
            $this->_conditionFactory->create('Customer_Storecredit')->getNewChildSelectOptions());
        return array(
            'value' => $conditions,
            'label' => __('Customer'),
        );
    }
}
