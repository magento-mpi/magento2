<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Shipping
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getShippingAmount();
        if ($amount!=0) {
            $address->addTotal(array(
                'code'=>'shipping', 
                'title'=>__('Shipping & Handling').' ('.$address->getShippingDescription().')', 
                'value'=>$address->getShippingAmount()
            ));
        }
        return $this;
    }

}