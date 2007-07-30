<?php
/**
 * Stores collection
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Mysql4_Store_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function __construct()
    {
        parent::__construct();
        //$this->getSelect()->where($this->getConnection()->quoteInto('main_table.store_id>?', 0));
    }

    protected function _construct()
    {
        $this->_init('core/store');
    }

    public function addIdFilter($store)
    {
        if (is_array($store)) {
            $condition = $this->getConnection()->quoteInto("store_id IN (?)", $store);
        }
        else {
            $condition = $this->getConnection()->quoteInto("store_id=?",$store);
        }

        $this->addFilter('store_id', $condition, 'string');
        return $this;
    }
    
    public function addWebsiteFilter($website)
    {
        if (is_array($website)) {
            $condition = $this->getConnection()->quoteInto("website_id IN (?)", $website);
        }
        else {
            $condition = $this->getConnection()->quoteInto("website_id=?",$website);
        }

        $this->addFilter('website_id', $condition, 'string');
        return $this;
    }

    public function addCategoryFilter($category)
    {
        if (is_array($category)) {
            $condition = $this->getConnection()->quoteInto("root_category_id IN (?)", $category);
        }
        else {
            $condition = $this->getConnection()->quoteInto("root_category_id=?",$category);
        }

        $this->addFilter('category', $condition, 'string');
        return $this;
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('store_id', 'name');
    }

    public function toOptionHash()
    {
        return $this->_toOptionHash('store_id', 'name');
    }

}