<?php
/**
 * Product attributes collection
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Collection extends Varien_Data_Collection_Db
{
    protected $_attributeTable;
    protected $_attributeInSetTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('catalog_read'));
        
        $this->_attributeTable      = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute');
        $this->_attributeInSetTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_in_set');
        
        $this->_sqlSelect->from($this->_attributeTable);
        $this->_sqlSelect->join($this->_attributeInSetTable, "$this->_attributeTable.attribute_id=$this->_attributeInSetTable.attribute_id");
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog', 'product_attribute'));
    }
    
    /**
     * Add filter by product attribute set id
     *
     * @param int $attributeSetId
     */
    public function addSetFilter($attributeSetId)
    {
        $this->addFilter("$this->_attributeInSetTable.product_attribute_set_id", $attributeSetId);
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