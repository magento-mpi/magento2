<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Subtotal
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    public function getTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $arr['subtotal'] = array('code'=>'subtotal', 'title'=>__('Subtotal'), 'value'=>$address->getSubtotal(), 'output'=>true);

        return $arr;
    }
}