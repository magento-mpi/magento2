<?php

class Mage_Usa_Model_Shipping_Carrier_Dhl_Source_Method
{
    public function toOptionArray()
    {
        $dhl = Mage::getSingleton('usa/shipping_carrier_dhl');
        $arr = array();
        foreach ($dhl->getCode('service') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
