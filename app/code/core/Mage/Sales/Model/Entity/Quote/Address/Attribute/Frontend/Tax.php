<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Tax
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getTaxAmount();
        if ($amount!=0) {
            $address->addTotal(array(
                'code'=>'tax', 
                'title'=>__('Tax'), 
                'value'=>$amount
            ));
        }
        return $this;
    }
}