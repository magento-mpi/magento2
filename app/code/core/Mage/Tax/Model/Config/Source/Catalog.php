<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Tax_Model_Config_Source_Catalog
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('Mage_Tax_Helper_Data')->__('No (price without tax)')),
            array('value'=>1, 'label'=>Mage::helper('Mage_Tax_Helper_Data')->__('Yes (only price with tax)')),
            array('value'=>2, 'label'=>Mage::helper('Mage_Tax_Helper_Data')->__("Both (without and with tax)")),
        );
    }

}
