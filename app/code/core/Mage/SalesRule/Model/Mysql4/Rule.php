<?php

class Mage_SalesRule_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract 
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/rule', 'rule_id');
    }

    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setFromDate($this->formatDate($object->getFromDate()));
        $object->setToDate($this->formatDate($object->getToDate()));
        parent::_beforeSave($object);
    }
    
    public function _afterLoad(Mage_Core_Model_Abstract $object)
    {
    	if (0==$object->getDiscountQty()) {
    		$object->setDiscountQty('');
    	}
    	parent::_afterLoad($object);
    }
    
    public function updateRuleProductData(Mage_SalesRule_Model_Rule $rule)
    {
        foreach ($rule->getActions()->getActions() as $action) {
            break;
        }

        $ruleId = $rule->getId();

        $read = $this->getConnection('read');
        $write = $this->getConnection('write');
        
        $write->delete($this->getTable('salesrule/rule_product'), $write->quoteInto('rule_id=?', $ruleId));
        
        if (!$rule->getIsActive()) {
            return $this;
        }
        
        if ($rule->getUsesPerCoupon()>0) {
        	$usedPerCoupon = $read->fetchOne('select count(*) from salesrule_customer where rule_id=?', $ruleId);
        	if ($usedPerCoupon>=$rule->getUsesPerCoupon()) {
        		return $this;
        	}
        }
        
        $productIds = explode(',', $rule->getProductIds());
        $storeIds = explode(',', $rule->getStoreIds());
        $customerGroupIds = explode(',', $rule->getCustomerGroupIds());
        
        $fromTime = strtotime($rule->getFromDate());
        $toTime = strtotime($rule->getToDate());
        $couponCode = $rule->getCouponCode();
        $sortOrder = (int)$rule->getSortOrder();
        $actionOperator = $rule->getSimpleAction();
        $actionAmount = $rule->getDiscountAmount();
        $actionStop = $rule->getStopRulesProcessing();
        $freeShipping = (int)$rule->getSimpleFreeShipping();

        $rows = array();
        $header = 'replace into '.$this->getTable('salesrule/rule_product').' (rule_id, from_time, to_time, store_id, customer_group_id, product_id, coupon_code, sort_order, action_operator, action_value, action_stop, free_shipping) values ';
        try {
            $write->beginTransaction();
            
            foreach ($productIds as $productId) {
                foreach ($storeIds as $storeId) {
                    foreach ($customerGroupIds as $customerGroupId) {
                        $rows[] = "('$ruleId', '$fromTime', '$toTime', '$storeId', '$customerGroupId', '$productId', '$couponCode', '$sortOrder', '$actionOperator', '$actionAmount', '$actionStop', '$freeShipping')";
                        if (sizeof($rows)==100) {
                            $sql = $header.join(',', $rows);
                            $write->query($sql);
                            $rows = array();
                        }
                    }
                }
            }
            if (!empty($rows)) {
                $sql = $header.join(',', $rows);
                $write->query($sql);
            }
            
            $write->commit();
        } catch (Exception $e) {
            
            $write->rollback();
            throw $e;
            
        }
        
        return $this;
    }
    
    public function getCustomerUses($customerId)
    {
    	$read = $this->getConnection('read');
    	$select = $read->select()->from($this->getTable('rule_customer'), array('cnt'=>'count(*)'))
    		->where('rule_id=?', $this->getId())
    		->where('customer_id=?', $customerId);
    	return $read->fetchOne($select);
    }
}