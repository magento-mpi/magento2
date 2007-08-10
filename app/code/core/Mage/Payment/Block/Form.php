<?php

class Mage_Payment_Block_Form extends Mage_Core_Block_Template 
{
    public function getTitle()
    {
        return Mage::getStoreConfig('payment/'.$this->getMethod().'/title');
    }
    
    public function isCurrent()
    {
        return $this->getPayment() && $this->getMethod() 
            && $this->getPayment()->getMethod() == $this->getMethod();
    }
}