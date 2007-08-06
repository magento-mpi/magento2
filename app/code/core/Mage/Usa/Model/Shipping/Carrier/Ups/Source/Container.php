<?php

class Mage_Usa_Model_Shipping_Carrier_Ups_Source_Container
{
    public function toOptionArray()
    {
        $ups = Mage::getSingleton('usa/shipping_carrier_ups');
        $arr = array();
        foreach ($ups->getCode('container_description') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}