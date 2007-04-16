<?php
/**
 * Product attribute model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute extends Mage_Catalog_Model_Mysql4 implements Mage_Core_Model_Db_Table_Interface 
{
    protected $_attributeTable;
    
    public function __construct()
    {
        $this->_attributeTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute');
    }
    
    /**
     * Insert row in database table
     *
     * @param   array $data
     * @return  int || false
     */
    public function insert($data)
    {
        if (self::$_write->insert($this->_attributeTable, $data)) {
            return self::$_write->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update row in database table
     *
     * @param   array $data
     * @param   int   $rowId
     * @return  int
     */
    public function update($data, $rowId)
    {
        $condition = self::$_write->quoteInto('attribute_id=?', $rowId);
        return self::$_write->update($this->_attributeTable, $data, $condition);
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        $condition = self::$_write->quoteInto('attribute_id=?', $rowId);
        return self::$_write->delete($this->_attributeTable, $condition);
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function getRow($rowId)
    {
        $sql = "SELECT * FROM $this->_attributeTable WHERE attribute_id=:attribute_id";
        return self::$_read->fetchRow($sql, array('attribute_id'=>$rowId));
    }    
}