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
    
    public function __construct() 
    {
        parent::__construct(Mage::getResourceModel('catalog')->getReadConnection());
        $this->_attributeTable    = Mage::registry('resources')->getTableName('catalog', 'product_attribute');
        
        $this->_sqlSelect->from($this->_attributeTable);
    }
}