<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_TargetRule_Model_Source_Rotation
{

    /**
     * Get data for Rotation mode selector
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Enterprise_TargetRule_Model_Rule::ROTATION_NONE =>
                __('Do not rotate'),
            Enterprise_TargetRule_Model_Rule::ROTATION_SHUFFLE =>
                __('Shuffle'),
        );
    }

}
