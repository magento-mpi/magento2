<?php

class Mage_Customer_Resource_Model_Mysql4_Address_Type extends Mage_Customer_Resource_Model_Mysql4 implements Mage_Core_Resource_Model_Db_Table_Interface
{
    protected $_typeTable = null;
    protected $_typeLinkTable = null;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->_typeTable = $this->_getTableName('customer', 'address_type');
        $this->_typeLinkTable = $this->_getTableName('customer', 'address_type_link');
    }
    
    /**
     * Address type can be identified by both id and name, choose the appropriate
     *
     * @param integer|string $id
     */
    protected function _getIdCondition($id)
    {
        if (is_numeric($id)) {
            $condition = $this->_read->quoteInto("$this->_typeTable.address_type_id=?", $id);
        } else {
            $condition = $this->_read->quoteInto("$this->_typeTable.address_type_code=?", $id);
        }
    }

     /**
     * Insert row in database table
     *
     * @param   array $data
     * @return  integer|false
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
     * @param   integer|string   $rowId
     * @return  int
     */
    public function update($data, $rowId)
    {
        return $this->_write->update($this->_typeTable, $data, $this->_getIdCondition($rowId));
    }
    
    /**
     * Delete row from database table
     *
     * @param   integer|string $rowId
     */
    public function delete($rowId)
    {
        return $this->_write->delete($this->_typeTable, $this->_getIdCondition($rowId));
    }
    
    /**
     * Get row from database table
     *
     * @param   integer|string $rowId
     * @return  Varien_Data_Object
     */
    public function getRow($rowId)
    {
        $select = $this->_read->select()->from($this->_typeTable)->where($this->_getIdCondition($rowId));
        return $this->_read->fetchRow($select);
    }    
    
    public function getCollection($condition)
    {
        // fetch all types for address
        $select = $this->_read->select()->from($this->_typeLinkTable);
        $select->join($this->_typeTable, 
            "$this->_typeTable.address_type_id=$this->_typeLinkTable.address_type_id", 
            "$this->_typeTable.address_type_code");
        $select->where($condition);
        $typesArr = $this->_read->fetchAll($select);
        return $typesArr;
    }
    
    public function getByAddressId($addressId)
    {
        $condition = $this->_read->quoteInto("$this->_typeLinkTable.address_id=?", $addressId);
        $typesArr = $this->getCollection($condition);
        
        // process result
        $types = array('primary_types'=>array(), 'alternative_types'=>array());
        foreach ($typesArr as $type) {
            $priority = $type['is_primary'] ? 'primary_types' : 'alternative_types';
            $types[$priority][$type['address_type_code']] = true;
        }
        
        return $types;
    }

    public function getByCustomerId($customerId)
    {
        $condition = $this->_read->quoteInto("$this->_typeLinkTable.customer_id=?", $customerId);
        $typesArr = $this->getCollection($condition);
        
        // process result
        $types = array('primary_types'=>array(), 'alternative_types'=>array());
        foreach ($typesArr as $type) {
            $priority = $type['is_primary'] ? 'primary_types' : 'alternative_types';
            $types[$type['address_id']][$priority][$type['address_type_code']] = true;
        }
        
        return $types;
    }
    
    public function saveAddressTypes($data)
    {
        
    }
    
    public function deleteAddressTypes($addressId)
    {
        
    }
    
    /**
     * Retrieve available address types with their name by language
     * 
     * Use specified field for key
     *
     * @param string $by code|id
     * @param string $langCode en
     * @return array
     */
    public function getAvailableTypes($by='code', $langCode='en')
    {
        $langTable = $this->_getTableName('customer', 'address_type_language');
        
        $select = $this->_read->select()->from($this->_typeTable)
            ->join($langTable, "$langTable.address_type_id=$this->_typeTable.address_type_id", "$langTable.address_type_name");
            
        $typesArr = $this->_read->fetchAll($select);
        $types = array();
        foreach ($typesArr as $type) {
            $types[$type['address_type_'.$by]] = $type;
        }

        return $types;
    }
}