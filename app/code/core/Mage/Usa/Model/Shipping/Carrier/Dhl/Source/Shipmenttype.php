<?php

class Mage_Usa_Model_Shipping_Carrier_Dhl_Source_Shipmenttype
{
    public function toOptionArray()
    {
        $fedex = Mage::getSingleton('usa/shipping_carrier_dhl');
        $arr = array();
        foreach ($fedex->getCode('shipment_type') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
