<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Subtotal
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $address->addTotal(array('code'=>'subtotal', 'title'=>__('Subtotal'), 'value'=>$address->getSubtotal(), 'output'=>true));

        return $this;
    }
}