<?php
/**
 * Product attributes group collection
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Resource_Model_Mysql4_Product_Attribute_Group_Collection extends Varien_Data_Collection_Db
{
    protected $_groupTable;
    protected $_inGroupTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getResourceModel('catalog')->getReadConnection());
        $this->_groupTable  = Mage::registry('resources')->getTableName('catalog', 'product_attribute_group');
        $this->_inGroupTable= Mage::registry('resources')->getTableName('catalog', 'product_attribute_in_group');
        
        $this->_sqlSelect->from($this->_groupTable);
        $this->_sqlSelect->join(
            $this->_inGroupTable, 
            new Zend_Db_Expr("$this->_groupTable.product_attribute_group_id=$this->_inGroupTable.product_attribute_group_id"),
            'product_attribute_group_id'
        );
    }
    
    public function addAttributeFilter($attribute)
    {
        if (is_array($attribute)) {
            $condition = $this->getConnection()->quoteInto("$this->_inGroupTable.attribute_id IN (?)",$attribute);
        }
        else {
            $condition = $this->getConnection()->quoteInto("$this->_inGroupTable.attribute_id=?",$attribute);
        }

        $this->addFilter('attribute', $condition, 'string');
    }
}