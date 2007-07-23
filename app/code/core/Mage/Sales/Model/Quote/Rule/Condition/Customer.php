<?php

class Mage_Sales_Model_Quote_Rule_Condition_Customer extends Mage_Rule_Model_Condition_Abstract
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
    
    public function asString($format='')
    {
        return 'Customer '.parent::asString();
    }
    
    /**
     * Enter description here...
     *
     * @todo create field `num_orders_completed` in customer
     * @param Mage_Sales_Model_Quote $quote
     * @return boolean
     */
    public function validate()
    {
        $customer = $this->getEnv()->getCustomer();
        switch ($this->getAttribute()) {
            case 'registered':
                return (bool)$customer;
                
            case 'first_time_buyer':
                return (bool)$customer && $customer->getNumOrdersCompleted()==0;
        }
        return parent::validate();
    }
}