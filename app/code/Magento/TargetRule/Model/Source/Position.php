<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_TargetRule_Model_Source_Position
{

    /**
     * Get data for Position behavior selector
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Magento_TargetRule_Model_Rule::BOTH_SELECTED_AND_RULE_BASED =>
                __('Both Selected and Rule-Based'),
            Magento_TargetRule_Model_Rule::SELECTED_ONLY =>
                __('Selected Only'),
            Magento_TargetRule_Model_Rule::RULE_BASED_ONLY =>
                __('Rule-Based Only'),
        );
    }

}
