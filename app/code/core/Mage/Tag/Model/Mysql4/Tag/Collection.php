<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag collection model
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Tag_Model_Mysql4_Tag_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected $_joinFlags = array();

    protected function _construct()
    {
        $this->_init('tag/tag');
    }

    public function load($printQuery = false, $logQuery = false)
    {
        return parent::load($printQuery, $logQuery);
    }

    public function setJoinFlag($table)
    {
        $this->_joinFlags[$table] = true;
        return $this;
    }

    public function getJoinFlag($table)
    {
        return isset($this->_joinFlags[$table]);
    }

    public function unsetJoinFlag($table=null)
    {
        if (is_null($table)) {
            $this->_joinFlags = array();
        } elseif ($this->getJoinFlag($table)) {
            unset($this->_joinFlags[$table]);
        }

        return $this;
    }


    public function addPopularity($limit=null)
    {
        $this->getSelect()
            ->joinLeft(array('relation'=>$this->getTable('tag/relation')), 'main_table.tag_id=relation.tag_id', array('tag_relation_id', 'popularity' => 'COUNT(DISTINCT relation.tag_relation_id)'))
            ->group('main_table.tag_id');
        if (! is_null($limit)) {
            $this->getSelect()->limit($limit);
        }

        $this->setJoinFlag('relation');
        return $this;
    }

    public function addSummary($storeId)
    {
        $joinCondition = $this->getConnection()->quoteInto('summary.store_id = ?', $storeId);
        $this->getSelect()
            ->joinLeft(array('summary'=>$this->getTable('tag/summary')), 'main_table.tag_id=summary.tag_id AND ' . $joinCondition);

        $this->setJoinFlag('summary');
        return $this;
    }

    public function addStoresVisibility()
    {
        $this->setJoinFlag('add_stores_after');
    }

    protected function _addStoresVisibility()
    {
        $tagIds = $this->getColumnValues('tag_id');

        if (sizeof($tagIds)>0) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('summary'), array('store_id','tag_id'))
                ->where('tag_id IN(?)', $tagIds);

        }

        foreach ($this as $item) {

        }

        return $this;
    }

    public function load($printQuery=false, $logQuery=false)
    {
        parent::load($printQuery, $logQuery);
        if ($this->getJoinFlag('add_stores_after')) {
            $this->_addStoresVisibility();
        }
        return $this;
    }

    public function addFieldToFilter($field, $condition)
    {
        if ($this->getJoinFlag('relation') && 'popularity' == $field) {
            // TOFIX
            $this->getSelect()->where($this->_getConditionSql('count(relation.tag_relation_id)', $condition));
        } elseif ($this->getJoinFlag('summary') && in_array($field, array('customers', 'products', 'uses', 'historical_uses', 'popularity'))) {
            $this->getSelect()->where($this->_getConditionSql('summary.'.$field, $condition));
        } else {
           parent::addFieldToFilter($field, $condition);
        }
        return $this;
    }

    /**
     * Get sql for get record count
     *
     * @return  string
     */

    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $countSelect = clone $this->_sqlSelect;
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $sql = $countSelect->__toString();
        // TOFIX
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select COUNT(DISTINCT main_table.tag_id) from ', $sql);
        return $sql;
    }

    public function addStoreFilter($storeId)
    {
        //$this->addFieldToFilter('main_table.store_id', $storeId);

        $this->getSelect()->join(array('summary_store'=>$this->getTable('summary')), 'main_table.tag_id = summary_store.tag_id AND summary_store.store_id = ' . (int) $storeId);
        if($this->getJoinFlag('relation')) {
            $this->getSelect()->where('relation.store_id = ?', $storeId);
        }

        return $this;
    }

    public function addStatusFilter($status)
    {
        $this->addFieldToFilter('main_table.status', $status);
        return $this;
    }

    public function addProductFilter($productId)
    {
        $this->addFieldToFilter('relation.product_id', $productId);
        return $this;
    }

    public function addCustomerFilter($customerId)
    {
        $this->getSelect()
            ->where('relation.customer_id = ?', $customerId);
        return $this;
    }

    public function joinRel()
    {
        $this->setJoinFlag('relation');
        $this->getSelect()->joinLeft(array('relation'=>$this->getTable('tag/relation')), 'main_table.tag_id=relation.tag_id');
        return $this;
    }
}