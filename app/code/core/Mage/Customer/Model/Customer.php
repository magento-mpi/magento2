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
    
    abstract public function load($customerId);
    
    public function validate()
    {
        $data = $this->getData();
        $arrData= $this->_prepareArray($data, array('firstname', 'lastname', 'email', 'password'));
        
        $this->_data = array();
        $this->_data['customer_email']      = $arrData['email'];
        $this->_data['customer_pass']       = $arrData['password'];
        $this->_data['customer_firstname']  = $arrData['firstname'];
        $this->_data['customer_lastname']   = $arrData['lastname'];
        $this->_data['customer_type_id']    = 1; // TODO: default or defined customer type
        
        $testCustomer = Mage::getModel('customer', 'customer');
        $testCustomer->loadByEmail($arrData['email']);
        if ($customer->getCustomerId()) {
            $this->_message = 'Your E-Mail Address already exists in our records - please log in with the e-mail address or create an account with a different address';
            return false;
        }
        return true;
    }
    
    public function validatePassword($password)
    {
        return $this->getCustomerPass()===$this->_hashPassword($password);
    }
    
    protected function _hashPassword($password)
    {
        return md5($password);
    }
    
    public function setCustomerPass($password)
    {
        $this->setData('customer_pass', $this->_hashPassword($password));
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
        $address->load($addressId);
        return $address;
    }
}