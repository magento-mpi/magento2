<?php

class Mage_Payment_Block_Info extends Mage_Core_Block_Text
{
    public function getTitle()
    {
        return Mage::getStoreConfig('payment/'.$this->getPayment()->getMethod().'/title');
    }
}