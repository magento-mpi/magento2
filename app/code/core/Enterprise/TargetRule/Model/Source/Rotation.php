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
                Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Do not rotate'),
            Enterprise_TargetRule_Model_Rule::ROTATION_SHUFFLE =>
                Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Shuffle'),
        );
    }

}
