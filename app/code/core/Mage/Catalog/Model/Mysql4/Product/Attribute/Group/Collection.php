<?php
/**
 * Product attributes group collection
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Group_Collection extends Varien_Data_Collection_Db
{
    protected $_groupTable;
    protected $_setTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('catalog_read'));
        $this->_groupTable  = Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute_group');
        $this->_setTable    = Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute_set');
        
        $this->_sqlSelect->from($this->_groupTable);
        $this->_sqlSelect->join($this->_setTable, "$this->_groupTable.set_id=$this->_setTable.set_id", 'set_id');
        $this->setOrder($this->_groupTable.'.position', 'asc');
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog/product_attribute_group'));
    }
    
    public function addSetFilter($setId)
    {
        $this->addFilter('set', $this->_conn->quoteInto($this->_groupTable.'.set_id=?', $setId), 'string');
        return $this;
    }
}
