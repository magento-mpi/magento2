<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Custbalance
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    public function getTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $arr = array();
        
        $custbalance = $address->getCustbalanceAmount();
        if ($custbalance) {
            $arr['custbalance'] = array('code'=>'custbalance', 'title'=>__('Store Credit'), 'value'=>-$custbalance, 'output'=>true);
        }

        return $arr;
    }
}
