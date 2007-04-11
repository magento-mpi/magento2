<?php
/**
 * Customer
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Customer_Model_Customer extends Varien_Data_Object
{
    /**
     * Customer address collection
     *
     * @var Varien_Data_Colection_Db
     */
    protected $_addresses;
    
    public function __construct($customer=false) 
    {
        parent::__construct();
        
        if (is_numeric($customer)) {
            $this->loadByCustomerId($customer);
        } elseif (is_array($customer)) {
            $this->setData($customer);
        }
    }
    
    abstract public function authenticate($login, $password);
    
    abstract public function load($customerId);
    
    abstract public function loadByEmail($customerEmail);
    
    abstract public function save();
    
    abstract public function delete();

    public function addAddress(Mage_Customer_Model_Address $address)
    {
        if (!$this->_addresses) {
            $this->_addresses = Mage::getModel('customer', 'address_collection');
        }
        
        $this->_addresses->addItem($address);
        return $this;
    }   
    
    public function getAddress($addresType='')
    {
        if ('' == $addresType) {
            return $this->getAddressCollection()->getFirstItem();
        }
        //TODO: get by address type
    }
    
    public function getAddressById($addressId)
    {
        $address = Mage::getConfig()->getModelClassName('customer', 'address');
        $address->load($addressId);
        return $address;
    }

    public function getAddressCollection($reload=false)
    {
        if ($this->_addresses && !$reload) {
            return $this->_addresses;
        }
        
        if ($this->getCustomerId()) {
            $this->_addresses = Mage::getModel('customer', 'address_collection')->loadByCustomerId($this->getCustomerId());
        }
        else {
            $this->_addresses = Mage::getModel('customer', 'address_collection');
        }
        
        return $this->_addresses;
    }
    
    public function setCustomerPassword($password)
    {
        $this->setData('password', $this->_hashPassword($password));
        return $this;
    }
        
    protected function _hashPassword($password)
    {
        return md5($password);
    }
    
    public function __sleep()
    {
        unset($this->_addresses);
        return parent::__sleep();
    }
}