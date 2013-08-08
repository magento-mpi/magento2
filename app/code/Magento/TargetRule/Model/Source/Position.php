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
                Mage::helper('Magento_TargetRule_Helper_Data')->__('Both Selected and Rule-Based'),
            Magento_TargetRule_Model_Rule::SELECTED_ONLY =>
                Mage::helper('Magento_TargetRule_Helper_Data')->__('Selected Only'),
            Magento_TargetRule_Model_Rule::RULE_BASED_ONLY =>
                Mage::helper('Magento_TargetRule_Helper_Data')->__('Rule-Based Only'),
        );
    }

}
