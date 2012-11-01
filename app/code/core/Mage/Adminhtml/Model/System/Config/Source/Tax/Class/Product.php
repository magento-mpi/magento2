<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Model_System_Config_Source_Tax_Class_Product
{
    public function toOptionArray()
    {
        return Mage::getModel('Mage_Tax_Model_Class_Source_Product')->toOptionArray();
    }
}
