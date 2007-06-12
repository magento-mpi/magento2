<?php

class Mage_Catalog_Model_Product_Rule_Environment extends Mage_Core_Model_Rule_Environment 
{
    /**
     * Collect application environment for rules filtering
     *
     * @return Mage_Catalog_Model_Product_Rule_Environment
     */
    public function collect()
    {
        parent::collect();
        
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
        
        Mage::dispatchEvent('catalog_product_rule_environment_collect', array('env'=>$this));
        
        return $this;
    }
}