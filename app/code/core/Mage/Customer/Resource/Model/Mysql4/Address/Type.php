<?php

class Mage_Customer_Resource_Model_Mysql4_Address_Type extends Mage_Customer_Resource_Model_Mysql4 implements Mage_Core_Resource_Model_Db_Table_Interface
{
    protected $_typeTable = null;
    
    public function __construct()
    {
        $this->_typeTable = $this->_getTableName('customer', 'address_type');
    }
    
    protected function _getIdCondition($id)
    {
        if (is_numeric($id)) {
            $condition = $this->_read->quoteInto('address_type_id=?', $id);
        } else {
            $condition = $this->_read->quoteInto('address_type_code=?', $id);
        }
    }

     /**
     * Insert row in database table
     *
     * @param   array $data
     * @return  int || false
     */
    public function insert($data)
    {
        if ($this->_write->insert($this->_typeTable, $data)) {
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
        return $this->_write->update($this->_typeTable, $data, $this->_getIdCondition($rowId));
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        return $this->_write->delete($this->_typeTable, $this->_getIdCondition($rowId));
    }
    
    /**
     * Get row from database table
     *
     * @param   int|string $rowId
     * @return  Varien_DataObject
     */
    public function getRow($rowId)
    {
        //return new Varien_DataObject($this->_read->fetchRow($sql, array('address_id'=>$rowId)));
        $select = $this->_read->select()->from($this->_typeTable)->where($this->_getIdCondition($rowId));
        return $this->_read->fetchRow($select);
    }    
    
    public function getCollection()
    {
        
    }
}