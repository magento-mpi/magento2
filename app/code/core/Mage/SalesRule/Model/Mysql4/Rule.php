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
    
    public function updateRuleProductData(Mage_CatalogRule_Model_Rule $rule)
    {
        foreach ($rule->getActions()->getActions() as $action) {
            break;
        }

        $ruleId = $rule->getId();

        $write = $this->getConnection('write');
        $write->delete($this->getTable('catalogrule/rule_product'), $write->quoteInto('rule_id=?', $ruleId));
        
        if (!$rule->getIsActive()) {
            return $this;
        }
        
        $productIds = $rule->getMatchingProductIds();
        $storeIds = explode(',', $rule->getStoreIds());
        $customerGroupIds = explode(',', $rule->getCustomerGroupIds());
        
        $fromTime = strtotime($rule->getFromDate());
        $toTime = strtotime($rule->getToDate());
        $sortOrder = (int)$rule->getSortOrder();
        $actionOperator = $action->getOperator();
        $actionAmount = $action->getValue();
        $actionStop = $rule->getStopRulesProcessing();

        $rows = array();
        $header = 'replace into '.$this->getTable('catalogrule/rule_product').' (rule_id, from_time, to_time, store_id, customer_group_id, product_id, action_operator, action_amount, action_stop, sort_order) values ';
        try {
            $write->beginTransaction();
            
            foreach ($productIds as $productId) {
                foreach ($storeIds as $storeId) {
                    foreach ($customerGroupIds as $customerGroupId) {
                        $rows[] = "($ruleId, $fromTime, $toTime, $storeId, $customerGroupId, $productId, '$actionOperator', $actionAmount, $actionStop, $sortOrder)";
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
}