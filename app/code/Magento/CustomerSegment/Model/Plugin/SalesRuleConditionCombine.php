<?php
/**
 * Plugin for Magento_SalesRule_Model_Rule_Condition_Combine model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_CustomerSegment_Model_Plugin_SalesRuleConditionCombine
{
    /**
     * @var Magento_CustomerSegment_Helper_Data
     */
    private $_segmentHelper;

    /**
     * @param Magento_CustomerSegment_Helper_Data $segmentHelper
     */
    public function __construct(Magento_CustomerSegment_Helper_Data $segmentHelper)
    {
        $this->_segmentHelper = $segmentHelper;
    }

    /**
     * Add Customer Segment condition to the salesrule management
     * after plugin for getNewChildSelectOptions method
     *
     * @param array $conditions
     * @return array
     */
    public function afterGetNewChildSelectOptions($conditions)
    {
        if ($this->_segmentHelper->isEnabled()) {
            $segmentCondition = array(
                array(
                    'label' => __('Customer Segment'),
                    'value' => 'Magento_CustomerSegment_Model_Segment_Condition_Segment'
                )
            );

            $conditions = array_merge_recursive($conditions, $segmentCondition);
        }

        return $conditions;
    }
}