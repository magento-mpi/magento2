<?php

class Mage_Customer_Model_Session extends Mage_Core_Model_Session
{
    public function __construct()
    {
        parent::__construct('customer');
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
                if (!empty($username) && !empty($password)) {
                    if ($this->login($username, $password)) {
                        $action->getResponse()->setRedirect($action->getRequest()->getRequestUri());
                        return false;
                    }
                    else {
                        Mage::getSingleton('customer_model', 'session')->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE000'));
                    }
                }
            }
            $block = Mage::createBlock('customer_login', 'customer.login')
                ->assign('messages',    Mage::getSingleton('customer_model', 'session')->getMessages(true));
            Mage::getBlock('content')->append($block);
            return false;
        }
        return true;
    }
}