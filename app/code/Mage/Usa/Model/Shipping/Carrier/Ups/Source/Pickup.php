<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Usa_Model_Shipping_Carrier_Ups_Source_Pickup
{
    public function toOptionArray()
    {
        $ups = Mage::getSingleton('Mage_Usa_Model_Shipping_Carrier_Ups');
        $arr = array();
        foreach ($ups->getCode('pickup') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>__($v['label']));
        }
        return $arr;
    }
}
