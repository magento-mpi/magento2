<?php

class Mage_CatalogRule_Model_Observer
{
    protected $_rulePrices = array();
    
    public function getFinalPrice($observer)
    {
        if ($observer->hasDate()) {
            $date = $observer->getDate();
        } else {
            $date = now();
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
            $gId = $custSession->isLoggedIn() ? $custSession->getCustomer()->getCustomerGroupId() : 0;
        }
        
        $product = $observer->getEvent()->getProduct();
        $pId = $product->getId();
        
        if (!isset($this->_rulePrices[$date][$sId][$gId][$pId])) {
            $rulePrice = Mage::getResourceModel('catalogrule/rule')
                ->getRulePrice($date, $sId, $gId, $pId);
            $this->_rulePrices[$date][$sId][$gId][$pId] = $rulePrice;
        }
        $finalPrice = min($product->getFinalPrice(), $this->_rulePrices[$id]);
        $product->setFinalPrice($finalPrice);
    }
    
    public function dailyCatalogUpdate($observer)
    {
        $resource = Mage::getResourceSingleton('catalogrule/rule');
        $resource->applyAllRulesForDateRange(
            $resource->formatDate(mktime(0,0,0,date('m'),date('d')+1)),
            $resource->formatDate(mktime(0,0,0,date('m'),date('d')+2))
        );
    }
}