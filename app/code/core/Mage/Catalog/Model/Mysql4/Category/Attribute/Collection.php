<?php
/**
 * Category attributes collection
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Category_Attribute_Collection extends Varien_Data_Collection_Db 
{
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('catalog_read'));
        
        $attributeTable     = Mage::registry('resources')->getTableName('catalog', 'category_attribute');
        $attributeSetTable  = Mage::registry('resources')->getTableName('catalog', 'category_attribute_set');
        
        $this->_sqlSelect->from($attributeTable);
        $this->_sqlSelect->join($attributeSetTable, "$attributeTable.attribute_id=$attributeSetTable.attribute_id");
    }
}