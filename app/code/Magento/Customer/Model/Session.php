<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer session model
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Session extends Magento_Core_Model_Session_Abstract
{
    /**
     * Customer object
     *
     * @var Magento_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Flag with customer id validations result
     *
     * @var bool
     */
    protected $_isCustomerIdChecked = null;

    /**
     * Customer data
     *
     * @var Magento_Customer_Helper_Data
     */
    protected $_customerData = null;

    /**
     * Core url
     *
     * @var Magento_Core_Helper_Url
     */
    protected $_coreUrl = null;

    /**
     * Retrieve customer sharing configuration model
     *
     * @return Magento_Customer_Model_Config_Share
     */
    public function getCustomerConfigShare()
    {
        return Mage::getSingleton('Magento_Customer_Model_Config_Share');
    }

    /**
     * Class constructor. Initialize session namespace
     *
     *
     *
     * @param Magento_Core_Helper_Url $coreUrl
     * @param Magento_Customer_Helper_Data $customerData
     * @param string $sessionName
     */
    public function __construct(
        Magento_Core_Helper_Url $coreUrl,
        Magento_Customer_Helper_Data $customerData,
        $sessionName = null
    ) {
        $this->_coreUrl = $coreUrl;
        $this->_customerData = $customerData;
        $namespace = 'customer';
        if ($this->getCustomerConfigShare()->isWebsiteScope()) {
            $namespace .= '_' . (Mage::app()->getStore()->getWebsite()->getCode());
        }

        $this->init($namespace, $sessionName);
        Mage::dispatchEvent('customer_session_init', array('customer_session'=>$this));
    }

    /**
     * Set customer object and setting customer id in session
     *
     * @param   Magento_Customer_Model_Customer $customer
     * @return  Magento_Customer_Model_Session
     */
    public function setCustomer(Magento_Customer_Model_Customer $customer)
    {
        // check if customer is not confirmed
        if ($customer->isConfirmationRequired()) {
            if ($customer->getConfirmation()) {
                return $this->_logout();
            }
        }
        $this->_customer = $customer;
        $this->setId($customer->getId());
        // save customer as confirmed, if it is not
        if ((!$customer->isConfirmationRequired()) && $customer->getConfirmation()) {
            $customer->setConfirmation(null)->save();
            $customer->setIsJustConfirmed(true);
        }
        return $this;
    }

    /**
     * Retrieve customer model object
     *
     * @return Magento_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if ($this->_customer instanceof Magento_Customer_Model_Customer) {
            return $this->_customer;
        }

        $customer = Mage::getModel('Magento_Customer_Model_Customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
        if ($this->getId()) {
            $customer->load($this->getId());
        }

        $this->setCustomer($customer);
        return $this->_customer;
    }

    /**
     * Set customer id
     *
     * @param int|null $id
     * @return Magento_Customer_Model_Session
     */
    public function setCustomerId($id)
    {
        $this->setData('customer_id', $id);
        return $this;
    }

    /**
     * Retrieve customer id from current session
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        if ($this->getData('customer_id')) {
            return $this->getData('customer_id');
        }
        return ($this->isLoggedIn()) ? $this->getId() : null;
    }

    /**
     * Set customer group id
     *
     * @param int|null $id
     * @return Magento_Customer_Model_Session
     */
    public function setCustomerGroupId($id)
    {
        $this->setData('customer_group_id', $id);
        return $this;
    }

    /**
     * Get customer group id
     * If customer is not logged in system, 'not logged in' group id will be returned
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        if ($this->getData('customer_group_id')) {
            return $this->getData('customer_group_id');
        }
        if ($this->isLoggedIn() && $this->getCustomer()) {
            return $this->getCustomer()->getGroupId();
        }
        return Magento_Customer_Model_Group::NOT_LOGGED_IN_ID;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return (bool)$this->getId() && (bool)$this->checkCustomerId($this->getId());
    }

    /**
     * Check exists customer (light check)
     *
     * @param int $customerId
     * @return bool
     */
    public function checkCustomerId($customerId)
    {
        if ($this->_isCustomerIdChecked === null) {
            $this->_isCustomerIdChecked = Mage::getResourceSingleton('Magento_Customer_Model_Resource_Customer')
                ->checkCustomerId($customerId);
        }
        return $this->_isCustomerIdChecked;
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
        /** @var $customer Magento_Customer_Model_Customer */
        $customer = Mage::getModel('Magento_Customer_Model_Customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());

        if ($customer->authenticate($username, $password)) {
            $this->setCustomerAsLoggedIn($customer);
            $this->renewSession();
            return true;
        }
        return false;
    }

    public function setCustomerAsLoggedIn($customer)
    {
        $this->setCustomer($customer);
        Mage::dispatchEvent('customer_login', array('customer'=>$customer));
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
        $customer = Mage::getModel('Magento_Customer_Model_Customer')->load($customerId);
        if ($customer->getId()) {
            $this->setCustomerAsLoggedIn($customer);
            return true;
        }
        return false;
    }

    /**
     * Logout customer
     *
     * @return Magento_Customer_Model_Session
     */
    public function logout()
    {
        if ($this->isLoggedIn()) {
            Mage::dispatchEvent('customer_logout', array('customer' => $this->getCustomer()) );
            $this->_logout();
        }
        return $this;
    }

    /**
     * Authenticate controller action by login customer
     *
     * @param   Magento_Core_Controller_Varien_Action $action
     * @param   bool $loginUrl
     * @return  bool
     */
    public function authenticate(Magento_Core_Controller_Varien_Action $action, $loginUrl = null)
    {
        if ($this->isLoggedIn()) {
            return true;
        }

        $this->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_current' => true)));
        if (isset($loginUrl)) {
            $action->getResponse()->setRedirect($loginUrl);
        } else {
            $action->setRedirectWithCookieCheck(Magento_Customer_Helper_Data::ROUTE_ACCOUNT_LOGIN,
                $this->_customerData->getLoginUrlParams()
            );
        }

        return false;
    }

    /**
     * Set auth url
     *
     * @param string $key
     * @param string $url
     * @return Magento_Customer_Model_Session
     */
    protected function _setAuthUrl($key, $url)
    {
        $url = $this->_coreUrl
            ->removeRequestParam($url, Mage::getSingleton('Magento_Core_Model_Session')->getSessionIdQueryParam());
        // Add correct session ID to URL if needed
        $url = Mage::getModel('Magento_Core_Model_Url')->getRebuiltUrl($url);
        return $this->setData($key, $url);
    }

    /**
     * Logout without dispatching event
     *
     * @return Magento_Customer_Model_Session
     */
    protected function _logout()
    {
        $this->setId(null);
        $this->setCustomerGroupId(Magento_Customer_Model_Group::NOT_LOGGED_IN_ID);
        $this->getCookie()->delete($this->getSessionName());
        return $this;
    }

    /**
     * Set Before auth url
     *
     * @param string $url
     * @return Magento_Customer_Model_Session
     */
    public function setBeforeAuthUrl($url)
    {
        return $this->_setAuthUrl('before_auth_url', $url);
    }

    /**
     * Set After auth url
     *
     * @param string $url
     * @return Magento_Customer_Model_Session
     */
    public function setAfterAuthUrl($url)
    {
        return $this->_setAuthUrl('after_auth_url', $url);
    }

    /**
     * Reset core session hosts after reseting session ID
     *
     * @return Magento_Customer_Model_Session
     */
    public function renewSession()
    {
        parent::renewSession();
        $this->_cleanHosts();
        return $this;
    }
}
