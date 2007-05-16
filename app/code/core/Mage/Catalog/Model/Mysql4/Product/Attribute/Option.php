<?php
/**
 * Product attributes options model
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Option
{
    protected $_read;
    protected $_write;
    protected $_optionTable;
    
    public function __construct() 
    {
        $this->_read            = Mage::registry('resources')->getConnection('catalog_read');
        $this->_write           = Mage::registry('resources')->getConnection('catalog_write');
        $this->_optionTable     = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_option');
    }

    public function load($optionId)
    {
        return $this->_read->fetchRow("SELECT * FROM $this->_optionTable WHERE option_id=:id", array('id'=>$optionId));
    }    
    
    public function save()
    {
        return $this;
    }
}