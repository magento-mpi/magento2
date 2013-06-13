<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payment from Applicable Countries for Ogone Direct Debit
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento
 */
class Enterprise_Pbridge_Model_System_Config_Source_Ogone_Country
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'AT',
                'label' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Austria')
            ),
            array(
                'value' => 'DE',
                'label' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Germany')
            ),
            array(
                'value' => 'NL',
                'label' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Netherlands')
            ),
        );
    }
}
