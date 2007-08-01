<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Shipping
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    public function getTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $arr = array();
        $amount = $address->getShippingAmount();
        if ($amount) {
            $arr['shipping'] = array('code'=>'shipping', 'title'=>__('Shipping & Handling').' ('.$address->getShippingDescription().')', 'value'=>$quote->getShippingAmount(), 'output'=>true);
        }
        return $arr;
    }

}