<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Adminhtml_Model_System_Config_Source_Enabledisable
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Enable')),
            array('value'=>0, 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Disable')),
        );
    }
}
