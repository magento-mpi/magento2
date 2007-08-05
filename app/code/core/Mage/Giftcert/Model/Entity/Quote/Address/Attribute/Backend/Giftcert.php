<?php

class Mage_Giftcert_Model_Entity_Quote_Address_Attribute_Backend_Giftcert
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend
{
    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $gift = Mage::getResourceModel('sales/giftcert')->getGiftcertByCode($address->getGiftcertCode());
        if ($gift) {
            $address->setGiftcertAmount(min($address->getGrandTotal(), $gift['balance_amount']));
        } else {
            $address->setGiftcertAmount(0);
        }
        
        $address->setGrandTotal($address->getGrandTotal() - $address->getGiftcertAmount());
        
        return $this;
    }

}
