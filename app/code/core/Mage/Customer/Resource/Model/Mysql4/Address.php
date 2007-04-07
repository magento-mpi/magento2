<?php
/**
 * Customer address model
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Resource_Model_Mysql4_Address extends Mage_Customer_Resource_Model_Mysql4 implements Mage_Core_Resource_Model_Db_Table_Interface
{
    protected $_addressTable = null;
    protected $_typeModel = null;
    
    public function __construct() 
    {
        parent::__construct();
        $this->_addressTable = $this->_getTableName('customer', 'address');
        $this->_typeModel = Mage::getResourceModel('customer', 'Address_Type');
    }
    
     /**
     * Insert row in database table
     *
     * @param   array $data
     * @return  int || false
     */
    public function insert($data)
    {
        $origData = $data;
        unset($data['primary_types'], $data['alternative_types']);
        
        if ($this->_write->insert($this->_addressTable, $data)) {
            $this->_typeModel->saveAddressTypes($origData);
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
        $origData = $data;
        unset($data['primary_types'], $data['alternative_types']);
        
        $condition = $this->_write->quoteInto('address_id=?', $rowId);
        $result = $this->_write->update($this->_addressTable, $data, $condition);
        if ($result) {
            $this->_typeModel->saveAddressTypes($origData);
        }
        return $result;
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        $condition = $this->_write->quoteInto('address_id=?', $rowId);
        $result = $this->_write->delete($this->_addressTable, $condition);
        $this->_typeModel->deleteAddressTypes($rowId);
        return $result;
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     * @return  Varien_Data_Object
     */
    public function getRow($rowId)
    {
        $select = $this->_read->select()->from($this->_addressTable)->where('address_id=?', $rowId);
        $address = $this->_read->fetchRow($select);
        
        $types = $this->_typeModel->getByAddressId($address['address_id']);
        $address['primary_types'] = $types['primary_types'];
        $address['alternative_types'] = $types['alternative_types'];
        
        return $address;
    }    
}