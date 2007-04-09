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
    protected $_addresses = null;
    
    public function __construct($customer=false) 
    {
        parent::__construct();
        
        if (is_numeric($customer)) {
            $this->loadByCustomerId($customer);
        } elseif (is_array($customer)) {
            $this->setData($customer);
        }
    }
    
    public function load($customerId)
    {
        $this->loadByCustomerId($customerId);
    }
    
    public function validateCreate()
    {
        return true;
    }
    
    public function validateUpdate()
    {
        return true;
    }
    
    public function validateChangePassword($data)
    {
        return true;
    }
        
    public function loadAddresses()
    {
        $this->_addresses = Mage::getModel('customer', 'address_collection');
        $this->_addresses->loadByCustomerId($this->getCustomerId());
    }
    
    public function getAddress($addressId)
    {
        $address = Mage::getConfig()->getModelClassName('customer', 'address');
        $address->loadByAddressId($addressId);
        return $address;
    }
}