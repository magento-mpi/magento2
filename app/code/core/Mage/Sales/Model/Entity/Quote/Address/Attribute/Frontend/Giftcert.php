<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Giftcert
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend
{
    public function getTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $arr = array();
        
        $amount = $address->getGiftcertAmount();
        if ($amount) {
            $arr['giftcert'] = array('code'=>'giftcert', 'title'=>__('Gift Certificate').' ('.$address->getGiftcertCode().')', 'value'=>-$amount, 'output'=>true);
        }

        return $arr;
    }
}
