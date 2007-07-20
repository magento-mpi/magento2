<?php
/**
 * Customer session model
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Model_Session extends Mage_Core_Model_Session_Abstract
{
    protected $_customer;
    
    public function __construct()
    {
        $this->init('customer');
        Mage::dispatchEvent('initCustomerSession', array('customer_session'=>$this));
    }
    
    /**
     * Set customer object and setting customer id in session
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Customer_Model_Session
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        $this->setId($customer->getId());
        return $this;
    }
    
    /**
     * Retrieve costomer model object
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if ($this->_customer instanceof Mage_Customer_Model_Customer) {
            return $this->_customer;
        }
        
        $customer = Mage::getModel('customer/customer');
        if ($this->getId()) {
            $customer->load($this->getId());
        }
        $this->setCustomer($customer);
        return $this->_customer;
    }
    
    /**
     * Retrieve customer id from current session
     *
     * @return int || null
     */
    public function getCustomerId()
    {
        return $this->getCustomer()->getId();
    }
    
    /**
     * Checking custommer loggin status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        $customer = $this->getCustomer();
        return ($customer instanceof Mage_Customer_Model_Customer) && $customer->getId();
    }
    
    /**
     * Customer authorization
     *
     * @param   string $username
     * @param   string $password
     * @return  bool
     */
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
    
    /**
     * Authorization customer by identifier
     *
     * @param   int $customerId
     * @return  bool
     */
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
    
    /**
     * Logout customer
     *
     * @return Mage_Customer_Model_Session
     */
    public function logout()
    {
        if ($this->isLoggedIn()) {
            Mage::dispatchEvent('customerLogout', array('customer' => $this->getCustomer()) );
            $this->setId(null);
        }
        return $this;
    }
    
    /**
     * Authenticate controller action by login customer
     *
     * @param   Mage_Core_Controller_Varien_Action $action
     * @return  bool
     */
    public function authenticate(Mage_Core_Controller_Varien_Action $action)
    {
        if (!$this->isLoggedIn()) {
            Mage::getSingleton('customer/session')->setUrlBeforeAuthentication(
                $action->getRequest()->getRequestUri()
            );
            $action->getResponse()->setRedirect(Mage::getUrl('customer/account/login', array('_secure'=>true)));
            return false;
        }
        return true;
    }
}
