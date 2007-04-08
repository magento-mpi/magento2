<?php
/**
 * Customer address model
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Resource_Model_Mysql4_Address extends Mage_Customer_Resource_Model_Mysql4
{
    protected $_addressTable = null;
    protected $_typeModel = null;
    
    public function __construct() 
    {
        parent::__construct();
        $this->_addressTable = Mage::registry('resources')->getTableName('customer', 'address');
        $this->_typeModel = Mage::getResourceModel('customer', 'address_type');
    }
    
     /**
     * Insert row in database table
     *
     * @param   Mage_Customer_Address $data
     * @return  int || false
     */
    public function insert(Mage_Customer_Address $address)
    {
        if ($this->_write->insert($this->_addressTable, $address->getData())) {
            $address->setAddressId($this->_write->lastInsertId());
            $this->_typeModel->saveAddressTypes($address->getType(), $address->getAddressId());
            return $address->getAddressId();
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
    public function update(Mage_Customer_Address $address)
    {
        $condition = $this->_write->quoteInto('address_id=?', $address->getAddressId());
        $result = $this->_write->update($this->_addressTable, $address->getData(), $condition);
        if ($result) {
            $this->_typeModel->saveAddressTypes($address->getType(), $address->getAddressId());
        }
        return $result;
    }
    
    /**
     * Delete row from database table
     *
     * @param   Mage_Customer_Address|int $rowId
     */
    public function delete($address)
    {
        if ($address instanceof Mage_Customer_Address) {
            $address = $address->getAddressId();
        }
        $condition = $this->_write->quoteInto('address_id=?', $address);
        $result = $this->_write->delete($this->_addressTable, $condition);
        $this->_typeModel->deleteAddressTypes($address);
        return $result;
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     * @return  Mage_Customer_Address
     */
    public function getRow($addressId, $address=null)
    {
        $select = $this->_read->select()->from($this->_addressTable)->where('address_id=?', $addressId);
        $row = $this->_read->fetchRow($select);
        if (!$row) {
            return false;
        }
        if (is_null($address)) {
            $address = new Mage_Customer_Address($row);
        } else {
            $address->setData($row);
        }
        $address->setType($this->_typeModel->getByAddressId($address->getAddressId()));
        return $address;
    }    
}