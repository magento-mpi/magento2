<?php

class Mage_CatalogRule_Model_Observer
{
    protected $_rulePrices = array();
    
    public function getFinalPrice($observer)
    {
        if ($observer->hasDate()) {
            $date = $observer->getDate();
        } else {
            $date = mktime(0,0,0);
        }
        
        if ($observer->hasStoreId()) {
            $sId = $observer->getStoreId();
        } else {
            $sId = Mage::getSingleton('core/store')->getId();
        }
        
        if ($observer->hasCustomerGroupId()) {
            $gId = $observer->getCustomerGroupId();
        } else {
            $custSession = Mage::getSingleton('customer/session');
            $gId = $custSession->isLoggedIn() ? $custSession->getCustomer()->getCustomerGroup() : 0;
        }
        
        $product = $observer->getEvent()->getProduct();
        $pId = $product->getId();
        
        $key = "$date|$sId|$gId|$pId";
        if (!isset($this->_rulePrices[$key])) {
            $rulePrice = Mage::getResourceModel('catalogrule/rule')
                ->getRulePrice($date, $sId, $gId, $pId);
            $this->_rulePrices[$key] = $rulePrice;
        }
        if ($this->_rulePrices[$key]!==false) {
            $finalPrice = min($product->getData('final_price'), $this->_rulePrices[$key]);
            $product->setFinalPrice($finalPrice);
        }
        return $this;
    }
    
    public function dailyCatalogUpdate($observer)
    {
        $resource = Mage::getResourceSingleton('catalogrule/rule');
        $resource->applyAllRulesForDateRange(
            $resource->formatDate(mktime(0,0,0)),
            $resource->formatDate(mktime(0,0,0,date('m'),date('d')+1))
        );
    }
}