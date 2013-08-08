<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Shipping_Model_Config_Source_Flatrate implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=> Mage::helper('Mage_Shipping_Helper_Data')->__('None')),
            array('value'=>'O', 'label'=>Mage::helper('Mage_Shipping_Helper_Data')->__('Per Order')),
            array('value'=>'I', 'label'=>Mage::helper('Mage_Shipping_Helper_Data')->__('Per Item')),
        );
    }
}
