<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Pbridge_Model_System_Config_Source_Payone_BankcheckType
{
    public function toOptionArray()
    {
        return array (
            '0' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Regular Check'),
            '1' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Check Against POS Blacklist'),
        );
    }
}
