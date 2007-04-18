<?php
/**
 * Product attributes set
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Set
{
    protected $_setTable;
    protected $_inSetTable;

    static protected $_read;
    static protected $_write;

    
    public function __construct() 
    {
        self::$_read = Mage::registry('resources')->getConnection('catalog_read');
        self::$_write = Mage::registry('resources')->getConnection('catalog_write');

        $this->_setTable    = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_set');
        $this->_inSetTable  = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_in_set');
    }
    
    public function load($setId)
    {
        return self::$_read->fetchRow("SELECT * FROM $this->_setTable WHERE set_id=:id", array('id'=>$setId));
    }
}