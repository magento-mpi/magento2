<?php
/**
 * Customer address model
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4_Address extends Mage_Customer_Address
{
    static protected $_addressTable = null;
    static protected $_typeTable = null;
    static protected $_typeLinkTable = null;
    static protected $_read = null;
    static protected $_write = null;
    
    public function __construct($data=array()) 
    {
        parent::__construct($data);
        
        self::$_addressTable = Mage::registry('resources')->getTableName('customer', 'address');
        self::$_typeTable = Mage::registry('resources')->getTableName('customer', 'address_type');
        self::$_typeLinkTable = Mage::registry('resources')->getTableName('customer', 'address_type_link');
        self::$_read = Mage::registry('resources')->getConnection('customer_read');
        self::$_write = Mage::registry('resources')->getConnection('customer_write');
    }
    
     /**
     * Insert row in database table
     *
     * @param   Mage_Customer_Address $data
     * @return  int || false
     */
    public function insert()
    {
        if (self::$_write->insert(self::$_addressTable, $this->getData())) {
            $this->setAddressId(self::$_write->lastInsertId());
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
        $condition = self::$_write->quoteInto('address_id=?', $this->getAddressId());
        $result = self::$_write->update(self::$_addressTable, $this->getData(), $condition);
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
        $condition = self::$_write->quoteInto('address_id=?', $addressId);
        $result = self::$_write->delete(self::$_addressTable, $condition);
        $this->deleteTypes($this);
        return $result;
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     * @return  Mage_Customer_Address
     */
    public function loadByAddressId($addressId)
    {
        $select = self::$_read->select()->from(self::$_addressTable)
            ->where(self::$_read->quoteInto('address_id=?', $addressId));
        $row = self::$_read->fetchRow($select);
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
        $select = self::$_read->select()->from(self::$_typeLinkTable);
        $select->join(self::$_typeTable, 
            self::$_typeTable.".address_type_id=".self::$_typeLinkTable.".address_type_id", 
            self::$_typeTable.".address_type_code");
        $select->where($condition);
        $typesArr = self::$_read->fetchAll($select);
        return $typesArr;
    }
    
    public function getTypesByAddressId($addressId)
    {
        $condition = self::$_read->quoteInto(self::$_typeLinkTable.".address_id=?", $addressId);
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
        $condition = self::$_read->quoteInto(self::$_typeLinkTable.".customer_id=?", $customerId);
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
        
        $select = self::$_read->select()->from(self::$_typeTable)
            ->join($langTable, "$langTable.address_type_id=".self::$_typeTable.".address_type_id", "$langTable.address_type_name");
            
        $typesArr = self::$_read->fetchAll($select);
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
            $condition = self::$_read->quoteInto(self::$_typeTable.".address_type_id=?", $id);
        } else {
            $condition = self::$_read->quoteInto(self::$_typeTable.".address_type_code=?", $id);
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