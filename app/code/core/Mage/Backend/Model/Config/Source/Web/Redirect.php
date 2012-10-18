<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_Source_Web_Redirect
{

    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('No')),
            array('value' => 1, 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Yes (302 Found)')),
            array('value' => 301, 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Yes (301 Moved Permanently)')),
        );
    }

}
