<?php
/**
 * Customer address model
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4_Address extends Mage_Customer_Model_Mysql4 implements Mage_Core_Model_Db_Table_Interface
{
    protected $_addressTable;
    
    public function __construct() 
    {
        parent::__construct();
        $this->_addressTable = $this->_getTableName('customer_setup', 'customer_address');
    }
    
     /**
     * Insert row in database table
     *
     * @param   array $data
     * @return  int || false
     */
    public function insert($data)
    {
        if ($this->_write->insert($this->_addressTable, $data)) {
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
        $condition = $this->_write->quoteInto('address_id=?', $rowId);
        return $this->_write->update($this->_addressTable, $data, $condition);
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        $condition = $this->_write->quoteInto('address_id=?', $rowId);
        return $this->_write->delete($this->_addressTable, $condition);
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     * @return  Varien_DataObject
     */
    public function getRow($rowId)
    {
        $sql = "SELECT * FROM $this->_addressTable WHERE address_id=:address_id";
        return new Varien_DataObject($this->_read->fetchRow($sql, array('address_id'=>$rowId)));
    }    
    
    
}