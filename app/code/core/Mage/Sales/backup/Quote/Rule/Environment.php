<?php

class Mage_Sales_Model_Quote_Rule_Environment extends Mage_Rule_Model_Environment 
{
    /**
     * Collect application environment for rules filtering
     *
     * @todo make it not dependent on checkout module
     * @return Mage_Sales_Model_Quote_Rule_Environment
     */
    public function collect()
    {
        parent::collect();
        
        $quote = $this->getQuote();
        if (!$quote) {
            $coSess = Mage::getSingleton('checkout/session'); 
            $quote = $coSess->getQuote();
            $this->setQuote($quote);
        }
        
        $customer = $this->getCustomer();
        if (!$customer) {
            $custSess = Mage::getSingleton('customer/session');
            if ($custSess->isLoggedIn()) {
                $customer = $custSess->getCustomer();
                $this->setCustomer($customer);
            }
        }
        
        $this->setCustomerRegistered((bool)$customer);
        $this->setCustomerNewBuyer($customer->getNumOrdersCompleted()==0);
        
        $this->setCouponCode($quote->getCouponCode());
        
        Mage::dispatchEvent('sales_quote_rule_environment_collect', array('env'=>$this));
        
        return $this;
    }
}