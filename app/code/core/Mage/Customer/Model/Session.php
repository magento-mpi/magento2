<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer session model
 *
 * @category   Mage
 * @package    Mage_Customer
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
Varien_Profiler::start('TEST1: '.__METHOD__);
        $customer = Mage::getModel('customer/customer');
Varien_Profiler::stop('TEST1: '.__METHOD__);
Varien_Profiler::start('TEST2: '.__METHOD__);
        if ($this->getId()) {
            $customer->load($this->getId());
        }
Varien_Profiler::stop('TEST2: '.__METHOD__);
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

    public function setCustomerAsLoggedIn($customer)
    {
        $this->setCustomer($customer);
        Mage::dispatchEvent('customerLogin');
        return $this;
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
    public function authenticate(Mage_Core_Controller_Varien_Action $action, $loginUrl = null)
    {
        if (!$this->isLoggedIn()) {
            $this->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_current'=>true)));
            if (is_null($loginUrl)) {
                $loginUrl = Mage::getUrl('customer/account/login', array('_secure'=>true));
            }
            $action->getResponse()->setRedirect($loginUrl);
            return false;
        }
        return true;
    }
}
