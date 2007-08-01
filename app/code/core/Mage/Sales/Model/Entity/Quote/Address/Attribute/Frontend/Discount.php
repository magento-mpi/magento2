<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Discount
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    public function getTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $arr = array();
        
        $amount = $address->getDiscountAmount();
        if ($amount) {
            $arr['discount'] = array('code'=>'discount', 'title'=>__('Discount').' ('.$address->getCouponCode().')', 'value'=>-$amount, 'output'=>true);
        }

        return $arr;
    }

}