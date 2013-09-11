<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Usa\Model\Shipping\Carrier\Ups\Source;

class Unitofmeasure
{
    public function toOptionArray()
    {
        $unitArr = \Mage::getSingleton('Magento\Usa\Model\Shipping\Carrier\Ups')->getCode('unit_of_measure');
        $returnArr = array();
        foreach ($unitArr as $key => $val){
            $returnArr[] = array('value'=>$key,'label'=>$key);
        }
        return $returnArr;
    }
}
