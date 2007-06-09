<?php

class Mage_Sales_Model_Quote_Rule_Environment extends Varien_Object 
{
    /**
     * Collect application environment for rules filtering
     *
     * @todo make it not dependent on checkout module
     * @return Mage_Sales_Model_Quote_Rule_Environment
     */
    public function collect()
    {
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
        
        $this->setNow(time());
        
        $this->setCouponCode($quote->getCouponCode());
        
        Mage::dispatchEvent('salesQuoteRuleEnvironment_collect', array('env'=>$this));
        
        return $this;
    }
}