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
    const ERROR_SAVE                    = 'Mage_Customer_Model_Customer::ERROR_SAVE';
    const ERROR_EMAIL_EXIST             = 'Mage_Customer_Model_Customer::ERROR_EMAIL_EXIST';
    const ERROR_PASSWORD_CONFIRMATION   = 'Mage_Customer_Model_Customer::ERROR_PASSWORD_CONFIRMATION';
    const ERROR_EMPTY_CUSTOMER_ID       = 'Mage_Customer_Model_Customer::ERROR_EMPTY_CUSTOMER_ID';
    
    const SUCCES_CUSTOMER_SAVED         = '';
    
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
    
    abstract public function authenticate($login, $password);
    
    abstract public function load($customerId);
    
    abstract public function save();
    
    abstract public function delete();

    public function addAddress(Mage_Customer_Model_Address $address)
    {
        $this->_addresses[] = $address;
    }   
    
    public function getAddress($addressId)
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
        $this->_addresses = Mage::getModel('customer', 'address_collection')->loadByCustomerId($this->getCustomerId());
        return $this->_addresses;
    }
    
    public function setCustomerPassword($password)
    {
        $this->setData('password', $this->_hashPassword($password));
    }
        
    protected function _hashPassword($password)
    {
        return md5($password);
    }
}