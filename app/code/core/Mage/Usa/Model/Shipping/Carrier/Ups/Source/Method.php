<?php

class Mage_Usa_Model_Shipping_Carrier_Ups_Source_Method
{
    public function toOptionArray()
    {
        $ups = Mage::getSingleton('usa/shipping_carrier_ups');
        $arr = array();
        foreach ($ups->getCode('method') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
