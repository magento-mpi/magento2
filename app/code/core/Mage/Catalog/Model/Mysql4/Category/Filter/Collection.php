<?php
/**
 * Category filters collection
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Category_Filter_Collection extends Varien_Data_Collection_Db
{
    protected $_filterTable;
    protected $_attributeTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('catalog_read'));
        $this->_filterTable     = Mage::registry('resources')->getTableName('catalog_resource', 'category_filter');
        $this->_attributeTable  = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute');
        
        $this->_sqlSelect->from($this->_filterTable);
        $this->_sqlSelect->joinLeft($this->_attributeTable, "$this->_filterTable.attribute_id=$this->_attributeTable.attribute_id");
        $this->setOrder($this->_filterTable.'.position', 'asc');
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog', 'category_filter'));
    }
    
    public function addCategoryFilter($categoryId)
    {
        $this->addFilter($this->_filterTable.'.category_id', $categoryId);
        return $this;
    }
    
    public function getItemsById($id)
    {
        $arr = array();
        $arrId = (array) $id;
        foreach ($this as $item) {
            if (in_array($item->getId(), $arrId)) {
                $arr[] = $item;
            }
        }
        
        return $arr;
    }
}