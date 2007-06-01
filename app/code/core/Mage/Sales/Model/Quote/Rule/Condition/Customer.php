<?php

class Mage_Sales_Model_Quote_Rule_Condition_Customer extends Mage_Sales_Model_Quote_Rule_Condition_Abstract
{
    public function loadAttributes()
    {
        $this->setAttributeOption(array(
            'type'=>'Type',
            'registered'=>'Registered',
            'first_time_buyer'=>'First time buyer',
        ));
        return $this;
    }
    
    public function toString($format='')
    {
        return 'Customer '.parent::toString();
    }
    
    /**
     * Enter description here...
     *
     * @todo create field num_orders_made in customer
     * @param Mage_Sales_Model_Quote $quote
     * @return boolean
     */
    public function validateQuote(Mage_Sales_Model_Quote $quote)
    {
        $customer = $quote->getCustomer();
        if (!$customer) {
            $custSess = Mage::getSingleton('customer', 'session');
            if ($custSess->isLoggedIn()) {
                $customer = $custSess->getCustomer();
            }
        }
        switch ($this->getAttribute()) {
            case 'registered':
                return (bool)$customer;
                
            case 'first_time_buyer':
                return (bool)$customer && $customer->getNumOrdersMade()==0;
        }
        return $this->validateAttribute($quote->getData($this->getAttribute()));
    }
}