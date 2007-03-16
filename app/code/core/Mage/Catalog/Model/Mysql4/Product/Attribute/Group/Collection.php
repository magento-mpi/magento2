<?php
/**
 * Product attributes group collection
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Group_Collection extends Mage_Core_Model_Collection
{
    protected $_groupTable;
    protected $_inGroupTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getModel('catalog'));
        $this->_groupTable  = $this->_dbModel->getTableName('catalog_read', 'product_attribute_group');
        $this->_inGroupTable= $this->_dbModel->getTableName('catalog_read', 'product_attribute_in_group');
        
        $this->_sqlSelect->from($this->_groupTable);
    }
}