<?php
/**
 * Product attribute model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute
{
    static protected $_read;
    static protected $_write;

    protected $_attributeTable;
    
    public function __construct()
    {
        $this->_attributeTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute');
        self::$_read = Mage::registry('resources')->getConnection('catalog_read');
        self::$_write = Mage::registry('resources')->getConnection('catalog_write');
    }
    
    public function load($attributeId)
    {
        $sql = "SELECT * FROM $this->_attributeTable WHERE attribute_id=:attribute_id";
        return self::$_read->fetchRow($sql, array('attribute_id'=>$attributeId));
    }    

    public function loadByCode($attributeCode)
    {
        $sql = "SELECT * FROM $this->_attributeTable WHERE attribute_code=:attribute_code";
        return self::$_read->fetchRow($sql, array('attribute_code'=>$attributeCode));
    }    
}