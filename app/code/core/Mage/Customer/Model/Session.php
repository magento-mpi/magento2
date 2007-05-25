<?php

class Mage_Customer_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('customer');
        if ($this->isLoggedIn()) {
            Mage::getSingleton('core', 'session_visitor')->setCustomerId($this->getCustomer()->getCustomerId());
        }
    }
    
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_session->customer = $customer;
        return $this;
    }
    
    public function getCustomer()
    {
        if (!($this->_session->customer instanceof Mage_Customer_Model_Customer)) {
            $this->setCustomer(Mage::getModel('customer', 'customer'));
        }
        return $this->_session->customer;
    }
    
    public function getCustomerId()
    {
        return $this->getCustomer()->getCustomerId();
    }
    
    public function isLoggedIn()
    {
        $customer = $this->getCustomer();
        
        return ($customer instanceof Mage_Customer_Model_Customer) && $customer->getCustomerId();
    }
    
    public function login($username, $password)
    {
        $customer = Mage::getModel('customer', 'customer')->authenticate($username, $password);
        if ($customer) {
            $this->setCustomer($customer);
            Mage::dispatchEvent('customerLogin');
            return true;
        }
        return false;
    }
    
    public function loginById($customerId)
    {
        $customer = Mage::getModel('customer', 'customer')->load($customerId);
        if ($customer) {
            $this->setCustomer($customer);
            Mage::dispatchEvent('customerLogin');
            return true;
        }
        return false;
    }
    
    public function logout()
    {
        if ($this->isLoggedIn()) {
            unset($this->_session->customer);
            Mage::dispatchEvent('customerLogout');
        }
    }
    
    public function authenticate($action)
    {
        if (!$this->isLoggedIn()) {
            Mage::getSingleton('customer', 'session')->setUrlBeforeAuthentication($action->getRequest()->getRequestUri());
            $action->getResponse()->setRedirect(Mage::getUrl('customer', array('controller'=>'account', 'action'=>'login', '_secure'=>true)));
            return false;
        }
        return true;
    }
}