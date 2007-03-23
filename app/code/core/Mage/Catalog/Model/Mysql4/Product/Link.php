<?php
/**
 * Product link model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Link extends Mage_Catalog_Model_Mysql4 implements Mage_Core_Model_Db_Table_Interface 
{
    protected $_linkTable;
    
    public function __construct() 
    {
        $this->_linkTable = $this->getTableName('catalog_setup', 'product_link');
    }
    
    /**
     * Insert row in database table
     *
     * @param   array $data
     * @return  int || false
     */
    public function insert($data)
    {
        if ($this->_write->insert($this->_linkTable, $data)) {
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
        $condition = $this->_write->quoteInto('link_id=?', $rowId);
        return $this->_write->update($this->_linkTable, $data, $condition);
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        $condition = $this->_write->quoteInto('link_id=?', $rowId);
        return $this->_write->delete($this->_linkTable, $condition);
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function getRow($rowId)
    {
        $sql = "SELECT * FROM $this->_linkTable WHERE link_id=:link_id";
        return $this->_read->fetchRow($sql, array('link_id'=>$rowId));
    }    
}