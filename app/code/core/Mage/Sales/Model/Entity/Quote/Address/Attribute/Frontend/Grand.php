<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Grand
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    public function getTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $arr = array();

        $arr['grand_total'] = array('code'=>'grand_total', 'title'=>__('Grand Total'), 'value'=>$address->getGrandTotal(), 'output'=>true, 'style'=>'font-weight:bold');

        return $arr;
    }
}