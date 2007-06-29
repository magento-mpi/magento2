<?php
/**
 * Stores collection
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Mysql4_Store_Collection extends Varien_Data_Collection_Db
{
    protected $_storeTable;
    
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('core_read'));
        
        $this->_storeTable = $resource->getTableName('core/store');
        $this->_sqlSelect->from($this->_storeTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('core/store'));
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
}