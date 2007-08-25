<?php

class Mage_Sales_Model_Quote_Address_Total_Grand
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $address->addTotal(array(
            'code'=>$this->getCode(), 
            'title'=>__('Grand Total'), 
            'value'=>$address->getGrandTotal(),
            'area'=>'footer',
        ));
        return $this;
    }
}