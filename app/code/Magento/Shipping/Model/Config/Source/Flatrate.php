<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Shipping_Model_Config_Source_Flatrate implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=> Mage::helper('Magento_Shipping_Helper_Data')->__('None')),
            array('value'=>'O', 'label'=>Mage::helper('Magento_Shipping_Helper_Data')->__('Per Order')),
            array('value'=>'I', 'label'=>Mage::helper('Magento_Shipping_Helper_Data')->__('Per Item')),
        );
    }
}
