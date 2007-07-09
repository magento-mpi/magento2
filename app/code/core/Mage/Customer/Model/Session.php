<?php

class Mage_Customer_Model_Session extends Mage_Core_Model_Session_Abstract
{
    protected $_customer;
    
    public function __construct()
    {
        $this->init('customer');
        Mage::dispatchEvent('initCustomerSession', array('customer_session'=>$this));
    }

    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        $this->_session->customerId = $customer->getId();
        return $this;
    }

    public function getCustomer()
    {
        if ($this->_customer instanceof Mage_Customer_Model_Customer) {
            return $this->_customer;
        }
        
        $customer = Mage::getModel('customer/customer');
        if ($this->_session->customerId) {
            $customer->load($this->_session->customerId);
        }
        $this->setCustomer($customer);
        return $this->_customer;
    }

    public function getCustomerId()
    {
        return $this->getCustomer()->getId();
    }

    public function isLoggedIn()
    {
        $customer = $this->getCustomer();

        return ($customer instanceof Mage_Customer_Model_Customer) && $customer->getId();
    }

    public function login($username, $password)
    {
        $customer = Mage::getModel('customer/customer')->authenticate($username, $password);
        if ($customer && $customer->getId()) {
            $this->setCustomer($customer);
            Mage::dispatchEvent('customerLogin');
            return true;
        }
        return false;
    }

    public function loginById($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
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
            Mage::dispatchEvent('customerLogout', array('customer' => $this->getCustomer()) );
            unset($this->_session->customerId);
        }
    }

    public function authenticate($action)
    {
        if (!$this->isLoggedIn()) {
            Mage::getSingleton('customer/session')->setUrlBeforeAuthentication($action->getRequest()->getRequestUri());
            $action->getResponse()->setRedirect(Mage::getUrl('customer/account/login', array('_secure'=>true)));
            return false;
        }
        return true;
    }
}