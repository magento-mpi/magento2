<?php

class Mage_Usa_Model_Shipping_Carrier_Usps_Source_Freemethod extends Mage_Usa_Model_Shipping_Carrier_Usps_Source_Method
{
    public function toOptionArray()
    {
        $arr = parent::toOptionArray();
        array_unshift($arr, array('value'=>'', 'label'=>'None'));
        return $arr;
    }
}