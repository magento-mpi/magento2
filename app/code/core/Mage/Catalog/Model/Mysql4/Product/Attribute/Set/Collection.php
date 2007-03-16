<?php
/**
 * Product attributes set collection
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Set_Collection extends Mage_Core_Model_Collection
{
    protected $_setTable;
    protected $_inSetTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getModel('catalog'));
        $this->_setTable    = $this->_dbModel->getTableName('catalog_read', 'product_attribute_set');
        $this->_inSetTable  = $this->_dbModel->getTableName('catalog_read', 'product_attribute_in_set');
        
        $this->_sqlSelect->from($this->_setTable);
/*        $this->_sqlSelect->join(
            $this->_inSetTable, 
            new Zend_Db_Expr("$this->_setTable.product_attribute_set_id=$this->_inSetTable.product_attribute_set_id"),
            'attribute_id'
        );*/

    }
}