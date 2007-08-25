<?php

class Mage_Checkout_Block_Success extends Mage_Core_Block_Template 
{
    public function getRealOrderId()
    {
    	return Mage::getModel('sales/order')->load($this->getLastOrderId())->getIncrementId();
    }
}