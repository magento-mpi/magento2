<?php
/**
 * Product attributes set collection
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Set_Collection extends Varien_Data_Collection_Db
{
    protected $_setTable;
    protected $_inSetTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getResourceModel('catalog')->getReadConnection());
        $this->_setTable    = Mage::registry('resources')->getTableName('catalog', 'product_attribute_set');
        $this->_inSetTable  = Mage::registry('resources')->getTableName('catalog', 'product_attribute_in_set');
        
        $this->_sqlSelect->from($this->_setTable);
    }
}