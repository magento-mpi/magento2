<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Usa_Model_Shipping_Carrier_Ups_Source_Container implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $ups = Mage::getSingleton('Magento_Usa_Model_Shipping_Carrier_Ups');
        $arr = array();
        foreach ($ups->getCode('container_description') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>__($v));
        }
        return $arr;
    }
}
