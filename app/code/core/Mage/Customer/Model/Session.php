<?php

class Mage_Customer_Model_Session
{
    protected $_session = null;
    
    public function __construct()
    {
        $this->_session = new Zend_Session_Namespace('customer', Zend_Session_Namespace::SINGLE_INSTANCE);
    }
    
    public function setCustomer(Mage_Customer_Mode_Customer $customer)
    {
        $this->_session->customer = $customer;
        return $this;
    }
    
    public function getCustomer()
    {
        if (!$this->_session->customer) {
            $this->_session->customer = Mage::getModel('customer', 'customer');
        }
        return $this->_session->customer;
    }
    
    public function isLoggedIn()
    {
        return $this->_session->customer && $this->_session->customer->getCustomerId();
    }
    
    public function login($username, $password)
    {
        $customer = Mage::getModel('customer', 'customer')->authenticate($username, $password);
        if ($customer) {
            $this->_session->customer = $customer;
            Mage::dispatchEvent('customerLogin');
            return true;
        }
        return false;
    }
    
    public function authenticate($action)
    {
        if (empty($this->_session->customer)) {
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