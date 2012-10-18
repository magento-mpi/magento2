<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Model_System_Config_Source_Tax_Catalog
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('No (price without tax)')),
            array('value'=>1, 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Yes (only price with tax)')),
            array('value'=>2, 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__("Both (without and with tax)")),
        );
    }

}
