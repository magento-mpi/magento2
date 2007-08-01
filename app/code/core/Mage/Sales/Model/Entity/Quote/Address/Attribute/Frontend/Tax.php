<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Tax
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    public function getTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $arr = array();
        
        $amount = $address->getTaxAmount();
        if ($amount) {
            $arr['tax'] = array('code'=>'tax', 'title'=>__('Tax').' (CA '.number_format($address->getTaxPercent(),3).'%)', 'value'=>$amount, 'output'=>true);
        }

        return $arr;
    }
}