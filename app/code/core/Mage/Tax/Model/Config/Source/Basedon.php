<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_Source_Tax_Basedon
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'shipping', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('Shipping Address')),
            array('value'=>'billing', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('Billing Address')),
            array('value'=>'origin', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__("Shipping Origin")),
        );
    }

}
