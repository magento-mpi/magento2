<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Discount
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getDiscountAmount();
        if ($amount!=0) {
            $address->addTotal(array(
                'code'=>'discount', 
                'title'=>__('Discount').' ('.$address->getCouponCode().')', 
                'value'=>-$amount
            ));
        }
        return $this;
    }

}