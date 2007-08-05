<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Custbalance
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $custbalance = $address->getCustbalanceAmount();
        if ($custbalance) {
            $address->addTotal(array(
                'code'=>'custbalance', 
                'title'=>__('Store Credit'), 
                'value'=>-$custbalance
            ));
        }
        return $this;
    }
}
