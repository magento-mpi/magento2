<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Adminhtml_Model_System_Config_Source_Image_Adapter
{
    public function toOptionArray()
    {
        return array(
            Varien_Image_Adapter::ADAPTER_IM  => Mage::helper('Mage_Adminhtml_Helper_Data')->__('ImageMagick'),
            Varien_Image_Adapter::ADAPTER_GD2 => Mage::helper('Mage_Adminhtml_Helper_Data')->__('PHP Gd 2'),
        );
    }
}
