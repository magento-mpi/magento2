<?php
/**
 * Product attributes collection
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Collection extends Varien_Data_Collection_Db
{
    protected $_attributeTable;
    protected $_attributeInSetTable;
    
    protected $_inSetTableJoined;
    
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('catalog_read'));
        
        $this->_attributeTable      = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute');
        $this->_attributeInSetTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_in_set');
        
        $this->_sqlSelect->from($this->_attributeTable);
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog/product_attribute'));
    }
    
    protected function _joinInSetTable()
    {
        if (!$this->_inSetTableJoined) {
            $this->_sqlSelect->join($this->_attributeInSetTable, 
                                    "$this->_attributeTable.attribute_id=$this->_attributeInSetTable.attribute_id",
                                    array(
                                        "$this->_attributeInSetTable.attribute_id AS joined_attribute_id",
                                        "$this->_attributeInSetTable.group_id",
                                        "$this->_attributeInSetTable.set_id",
                                        "$this->_attributeInSetTable.position",
                                    ));
            $this->_inSetTableJoined = true;
        }
        return $this;
    }
    
    /**
     * Add filter by product attribute set id
     *
     * @param int $setId
     */
    public function addSetFilter($setId)
    {
        $this->_joinInSetTable();
        $this->addFilter("$this->_attributeInSetTable.set_id", $setId);
        return $this;
    }
    
    /**
     * Add filter by product attribute group id
     *
     * @param int $attributeSetId
     */
    public function addGroupFilter($groupId)
    {
        $this->_joinInSetTable();
        $this->addFilter("$this->_attributeInSetTable.group_id", $groupId);
        return $this;
    }

    public function getItemByCode($attributeCode)
    {
        foreach ($this as $attribute) {
            if ($attribute->getAttributeCode()==$attributeCode) {
                return $attribute;
            }
        }
        return new $this->_itemObjectClass();
    }
    
    public function getItemById($attributeId)
    {
        foreach ($this as $attribute) {
            if ($attribute->getId()==$attributeId) {
                return $attribute;
            }
        }
        return new $this->_itemObjectClass();
    }
    
    public function setPositionOrder()
    {
        $this->_joinInSetTable()->setOrder($this->_attributeInSetTable.'.position', 'asc');
        return $this;
    }

    /**
     * Get attributes with true multiple flag
     *
     * @return unknown
     */
    public function getMultiples()
    {
        $arr = array();
        foreach ($this as $attribute) {
            if ($attribute->isMultiple()) {
                $arr[] = $attribute;
            }
        }
        return $arr;
    }
}