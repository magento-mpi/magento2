<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Usa_Model_Shipping_Carrier_Ups_Source_Method
{
    public function toOptionArray()
    {
        $ups = Mage::getSingleton('Magento_Usa_Model_Shipping_Carrier_Ups');
        $arr = array();
        foreach ($ups->getCode('method') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>Mage::helper('Magento_Usa_Helper_Data')->__($v));
        }
        return $arr;
    }
}
