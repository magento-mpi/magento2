<?php
/**
 * Product attributes collection
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Collection extends Mage_Core_Model_Collection
{
    protected $_attributeTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getModel('catalog'));
        $this->_attributeTable    = $this->_dbModel->getTableName('catalog_setup', 'product_attribute');
        
        $this->_sqlSelect->from($this->_attributeTable);
    }
}