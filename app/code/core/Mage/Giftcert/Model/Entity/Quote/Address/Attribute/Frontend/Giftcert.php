<?php

class Mage_Giftcert_Model_Entity_Quote_Address_Attribute_Frontend_Giftcert
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getGiftcertAmount();
        if ($amount) {
            $address->addTotal(array('code'=>'giftcert', 'title'=>__('Gift Certificate').' ('.$address->getGiftcertCode().')', 'value'=>-$amount, 'output'=>true));
        }
        return $this;
    }
}
