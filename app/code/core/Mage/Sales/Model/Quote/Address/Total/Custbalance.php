<?php

class Mage_Sales_Model_Quote_Address_Total_Custbalance
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setCustbalanceAmount(0);
        
        $address->setGrandTotal($address->getGrandTotal() - $address->getCustbalanceAmount());
        
        return $this;
    }
}
