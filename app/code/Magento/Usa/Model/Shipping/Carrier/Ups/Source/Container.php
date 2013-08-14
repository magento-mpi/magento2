<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Usa_Model_Shipping_Carrier_Ups_Source_Container
{
    public function toOptionArray()
    {
        $ups = Mage::getSingleton('Magento_Usa_Model_Shipping_Carrier_Ups');
        $arr = array();
        foreach ($ups->getCode('container_description') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>Mage::helper('Magento_Usa_Helper_Data')->__($v));
        }
        return $arr;
    }
}
