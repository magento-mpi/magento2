<?php
/**
 * Customer address model
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Resource_Model_Mysql4_Address extends Mage_Customer_Address
{
    protected $_addressTable = null;
    protected $_typeTable = null;
    protected $_typeLinkTable = null;
    protected $_read = null;
    protected $_write = null;
    
    public function __construct($data=array()) 
    {
        parent::__construct($data);
        
        $this->_addressTable = Mage::registry('resources')->getTableName('customer', 'address');
        $this->_typeTable = Mage::registry('resources')->getTableName('customer', 'address_type');
        $this->_typeLinkTable = Mage::registry('resources')->getTableName('customer', 'address_type_link');
        $this->_read = Mage::registry('resources')->getConnection('customer_read');
        $this->_write = Mage::registry('resources')->getConnection('customer_write');
    }
    
     /**
     * Insert row in database table
     *
     * @param   Mage_Customer_Address $data
     * @return  int || false
     */
    public function insert()
    {
        if ($this->_write->insert($this->_addressTable, $this->getData())) {
            $this->setAddressId($this->_write->lastInsertId());
            $this->insertTypes();
            return $this->getAddressId();
        }
        return false;
    }
    
    /**
     * Update row in database table
     *
     * @param   Mage_Customer_Address $data
     * @param   int   $rowId
     * @return  int
     */
    public function update()
    {
        $condition = $this->_write->quoteInto('address_id=?', $this->getAddressId());
        $result = $this->_write->update($this->_addressTable, $this->getData(), $condition);
        if ($result) {
            $this->updateTypes();
        }
        return $result;
    }
    
    /**
     * Delete row from database table
     *
     * @param   Mage_Customer_Address|int $rowId
     */
    public function delete($addressId=null)
    {
        if (is_null($addressId)) {
            $addressId = $this->getAddressId();
        }
        $condition = $this->_write->quoteInto('address_id=?', $addressId);
        $result = $this->_write->delete($this->_addressTable, $condition);
        $this->deleteTypes($this);
        return $result;
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     * @return  Mage_Customer_Address
     */
    public function getByAddressId($addressId)
    {
        $select = $this->_read->select()->from($this->_addressTable)
            ->where($this->_read->quoteInto('address_id=?', $addressId));
        $row = $this->_read->fetchRow($select);
        if (!$row) {
            return false;
        }
        $this->setData($row);
        $this->setType($this->getTypesByAddressId($this->getAddressId()));
        return $this;
    }
    
    public function getTypesByCondition($condition)
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
    
    public function getTypesByAddressId($addressId)
    {
        $condition = $this->_read->quoteInto("$this->_typeLinkTable.address_id=?", $addressId);
        $typesArr = $this->getTypesByCondition($condition);
        
        // process result
        $types = array();
        foreach ($typesArr as $type) {
            $types[$type['address_type_code']] = array('is_primary'=>$type['is_primary']);
        }
        
        return $types;
    }
    
    public function getTypesByCustomerId($customerId)
    {
        $condition = $this->_read->quoteInto("$this->_typeLinkTable.customer_id=?", $customerId);
        $typesArr = $this->getTypesByCondition($condition);
        
        // process result
        $types = array();
        foreach ($typesArr as $type) {
            $types[$type['address_id']][$type['address_type_code']] = array('is_primary'=>$type['is_primary']);
        }
        
        return $types;
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
        $langTable = Mage::registry('resources')->getTableName('customer', 'address_type_language');
        
        $select = $this->_read->select()->from($this->_typeTable)
            ->join($langTable, "$langTable.address_type_id=$this->_typeTable.address_type_id", "$langTable.address_type_name");
            
        $typesArr = $this->_read->fetchAll($select);
        $types = array();
        foreach ($typesArr as $type) {
            $types[$type['address_type_'.$by]] = $type;
        }

        return $types;
    }
    
    /**
     * Address type can be identified by both id and name, choose the appropriate
     *
     * @param integer|string $id
     */
    public function getTypeIdCondition($id)
    {
        if (is_numeric($id)) {
            $condition = $this->_read->quoteInto("$this->_typeTable.address_type_id=?", $id);
        } else {
            $condition = $this->_read->quoteInto("$this->_typeTable.address_type_code=?", $id);
        }
    }
    
    public function insertTypes()
    {
        
    }
    
    public function updateTypes()
    {
        
    }
    
    public function deleteTypes()
    {
        
    }
}