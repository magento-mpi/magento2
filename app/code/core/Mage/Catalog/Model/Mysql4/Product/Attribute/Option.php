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
        $this->_read            = Mage::getSingleton('core/resource')->getConnection('catalog_read');
        $this->_write           = Mage::getSingleton('core/resource')->getConnection('catalog_write');
        $this->_optionTable     = Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute_option');
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