<?php
/**
 * Product link type model
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Link_Type extends Mage_Catalog_Model_Mysql4 implements Mage_Core_Model_Db_Table_Interface 
{
    protected $_linkTypeTable;
    
    public function __construct() 
    {
        $this->_linkTypeTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_link_type');
    }
    
    public function getAttributes()
    {
        
    }
    
    /**
     * Insert row in database table
     *
     * @param   array $data
     * @return  int || false
     */
    public function insert($data)
    {
        if ($this->_write->insert($this->_linkTypeTable, $data)) {
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
        $condition = $this->_write->quoteInto('link_type_id=?', $rowId);
        return $this->_write->update($this->_linkTypeTable, $data, $condition);
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        $condition = $this->_write->quoteInto('link_type_id=?', $rowId);
        return $this->_write->delete($this->_linkTypeTable, $condition);
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function getRow($rowId)
    {
        $sql = "SELECT * FROM $this->_linkTypeTable WHERE link_type_id=:type_id";
        return $this->_read->fetchRow($sql, array('type_id'=>$rowId));
    }    
}