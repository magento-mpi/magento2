<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Source;

class Position
{

    /**
     * Get data for Position behavior selector
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            \Magento\TargetRule\Model\Rule::BOTH_SELECTED_AND_RULE_BASED =>
                __('Both Selected and Rule-Based'),
            \Magento\TargetRule\Model\Rule::SELECTED_ONLY =>
                __('Selected Only'),
            \Magento\TargetRule\Model\Rule::RULE_BASED_ONLY =>
                __('Rule-Based Only'),
        );
    }

}
