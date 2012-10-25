<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Shipping_Model_Config_Source_Taxclass implements Mage_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $options = Mage::getModel('Mage_Tax_Model_Class_Source_Product')->toOptionArray();
        return $options;
    }

}
