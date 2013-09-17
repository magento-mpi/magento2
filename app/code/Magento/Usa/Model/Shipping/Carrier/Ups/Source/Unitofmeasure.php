<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Usa_Model_Shipping_Carrier_Ups_Source_Unitofmeasure implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $unitArr = Mage::getSingleton('Magento_Usa_Model_Shipping_Carrier_Ups')->getCode('unit_of_measure');
        $returnArr = array();
        foreach ($unitArr as $key => $val){
            $returnArr[] = array('value'=>$key,'label'=>$key);
        }
        return $returnArr;
    }
}
