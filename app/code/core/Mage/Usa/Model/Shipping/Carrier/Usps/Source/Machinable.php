<?php

class Mage_Usa_Model_Shipping_Carrier_Usps_Source_Machinable
{
    public function toOptionArray()
    {
        $usps = Mage::getSingleton('usa/shipping_carrier_usps');
        $arr = array();
        foreach ($usps->getCode('machinable') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
