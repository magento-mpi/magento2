<?php
class Mage_Weee_Model_Mysql4_Tax extends Mage_Core_Model_Mysql4_Abstract {
    protected function _construct()
    {
        $this->_init('weee/tax', 'value_id');
    }
    
    public function fetchOne($select)
    {
        return $this->_getReadAdapter()->fetchOne($select);
    }

    public function fetchCol($select)
    {
        return $this->_getReadAdapter()->fetchCol($select);
    }

    public function getProductAppliedPriceRules($product)
    {
        $now = strtotime(now());
        $table = $this->getTable('catalogrule/rule_product');
        $select = $this->_getReadAdapter()->select();
        $select->from($table)
            ->where('product_id = ?', $product->getId())
            ->where('website_id = ?', Mage::app()->getStore()->getWebsiteId())
            ->where('customer_group_id = ?', Mage::getSingleton('customer/session')->getCustomerGroupId())
            ->where('(from_time <= ? OR from_time = 0)', $now)
            ->where('(to_time >= ? OR to_time = 0)', $now);

        $select->order('sort_order');
        $result = $this->_getReadAdapter()->fetchAll($select);

        if ($result) {
            return $result;
        } else {
            return array();
        }
    }
}