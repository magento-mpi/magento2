<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Source_Catalog_TimeFormat
{
    public function toOptionArray()
    {
        return array(
            array('value' => '12h', 'label' => Mage::helper('Mage_Backend_Helper_Data')->__('12h AM/PM')),
            array('value' => '24h', 'label' => Mage::helper('Mage_Backend_Helper_Data')->__('24h')),
        );
    }
}
