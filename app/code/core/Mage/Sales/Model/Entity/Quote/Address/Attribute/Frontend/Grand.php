<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Grand
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $address->addTotal(array(
            'code'=>'grand_total', 
            'title'=>__('Grand Total'), 
            'value'=>$address->getGrandTotal(),
            'style'=>'font-weight:bold'
        ));
        return $this;
    }
}