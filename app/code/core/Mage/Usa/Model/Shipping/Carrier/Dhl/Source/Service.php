<?php

class Mage_Usa_Model_Shipping_Carrier_Dhl_Source_Service
{
    public function toOptionArray()
    {
        $fedex = Mage::getSingleton('usa/shipping_carrier_dhl');
        $arr = array();
        $arr[] = array('value'=>'', 'label'=>'None');
        foreach ($fedex->getCode('service') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
