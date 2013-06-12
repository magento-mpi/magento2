<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Pbridge_Model_System_Config_Source_Sagepay_Mode
{
    public function toOptionArray()
    {
        return array (
            '' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__('--Please Select--'),
            'sim' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__("Simulator"),
            'test' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__("Test"),
            'live' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__("Live")
        );
    }
}