<?php

class Mage_Customer_Model_Session
{
    protected $_session = null;
    
    public function __construct()
    {
        $this->_session = new Zend_Session_Namespace('customer', Zend_Session_Namespace::SINGLE_INSTANCE);
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
            $login = $action->getRequest()->getPost('login');
            if (!empty($login)) {
                extract($login);
                if (!empty($customer_email) && !empty($customer_pass)) {
                    if ($this->login($customer_email, $customer_pass)) {
                        $action->getResponse()->setRedirect($action->getRequest()->getRequestUri());
                        return false;
                    }
                }
            }
            $block = Mage::createBlock('customer_login', 'customer.login');
            Mage::getBlock('content')->append($block);
            return false;
        }
        return true;
    }
}