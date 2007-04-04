<?php
/**
 * Product attribute model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Resource_Model_Mysql4_Product_Attribute extends Mage_Catalog_Resource_Model_Mysql4 implements Mage_Core_Resource_Model_Db_Table_Interface 
{
    protected $_attributeTable;
    
    public function __construct()
    {
        $this->_attributeTable = $this->getTableName('catalog_setup', 'product_attribute');
    }
    
    /**
     * Insert row in database table
     *
     * @param   array $data
     * @return  int || false
     */
    public function insert($data)
    {
        if ($this->_write->insert($this->_attributeTable, $data)) {
            return $this->_write->lastInsertId();
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
        $condition = $this->_write->quoteInto('attribute_id=?', $rowId);
        return $this->_write->update($this->_attributeTable, $data, $condition);
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        $condition = $this->_write->quoteInto('attribute_id=?', $rowId);
        return $this->_write->delete($this->_attributeTable, $condition);
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function getRow($rowId)
    {
        $sql = "SELECT * FROM $this->_attributeTable WHERE attribute_id=:attribute_id";
        return $this->_read->fetchRow($sql, array('attribute_id'=>$rowId));
    }    
}