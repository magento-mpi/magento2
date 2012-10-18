<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Adminhtml_Model_System_Config_Source_Shipping_Flatrate
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=> Mage::helper('Mage_Adminhtml_Helper_Data')->__('None')),
            array('value'=>'O', 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Per Order')),
            array('value'=>'I', 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Per Item')),
        );
    }
}
