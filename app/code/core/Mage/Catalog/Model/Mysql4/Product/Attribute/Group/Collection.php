<?php
/**
 * Product attributes group collection
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Group_Collection extends Varien_Data_Collection_Db
{
    protected $_groupTable;
    protected $_inSetTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('catalog_read'));
        $this->_groupTable  = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_group');
        $this->_inSetTable  = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_in_set');
        
        $this->_sqlSelect->from($this->_groupTable);
        $this->_sqlSelect->join($this->_inSetTable, "$this->_groupTable.group_id=$this->_inSetTable.group_id", 'group_id');
        $this->distinct(true);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog', 'product_attribute_group'));
    }
    
    public function addSetFilter($setId)
    {
        $this->addFilter('set', $this->_conn->quoteInto($this->_inSetTable.'.set_id=?', $setId), 'string');
        return $this;
    }
}
