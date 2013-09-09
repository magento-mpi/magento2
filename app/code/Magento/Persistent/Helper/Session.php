<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Persistent Shopping Cart Data Helper
 *
 * @category   Magento
 * @package    Magento_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Persistent_Helper_Session extends Magento_Core_Helper_Data
{
    /**
     * Instance of Session Model
     *
     * @var null|Magento_Persistent_Model_Session
     */
    protected $_sessionModel;

    /**
     * Persistent customer
     *
     * @var null|Magento_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Is "Remember Me" checked
     *
     * @var null|bool
     */
    protected $_isRememberMeChecked;

    /**
     * Persistent data
     *
     * @var Magento_Persistent_Helper_Data
     */
    protected $_persistentData = null;

    /**
     * @param Magento_Core_Model_Event_Manager_Proxy $eventManager
     * @param Magento_Persistent_Helper_Data $persistentData
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config $config
     */
    public function __construct(
        Magento_Core_Model_Event_Manager_Proxy $eventManager,
        Magento_Persistent_Helper_Data $persistentData,
        Magento_Core_Helper_Http $coreHttp,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config $config
    ) {
        $this->_persistentData = $persistentData;
        parent::__construct($eventManager, $coreHttp, $context, $config);
    }

    /**
     * Get Session model
     *
     * @return Magento_Persistent_Model_Session
     */
    public function getSession()
    {
        if (is_null($this->_sessionModel)) {
            $this->_sessionModel = Mage::getModel('Magento_Persistent_Model_Session');
            $this->_sessionModel->loadByCookieKey();
        }
        return $this->_sessionModel;
    }

    /**
     * Force setting session model
     *
     * @param Magento_Persistent_Model_Session $sessionModel
     * @return Magento_Persistent_Model_Session
     */
    public function setSession($sessionModel)
    {
        $this->_sessionModel = $sessionModel;
        return $this->_sessionModel;
    }

    /**
     * Check whether persistent mode is running
     *
     * @return bool
     */
    public function isPersistent()
    {
        return $this->getSession()->getId() && $this->_persistentData->isEnabled();
    }

    /**
     * Check if "Remember Me" checked
     *
     * @return bool
     */
    public function isRememberMeChecked()
    {
        if (is_null($this->_isRememberMeChecked)) {
            //Try to get from checkout session
            $isRememberMeChecked = Mage::getSingleton('Magento_Checkout_Model_Session')->getRememberMeChecked();
            if (!is_null($isRememberMeChecked)) {
                $this->_isRememberMeChecked = $isRememberMeChecked;
                Mage::getSingleton('Magento_Checkout_Model_Session')->unsRememberMeChecked();
                return $isRememberMeChecked;
            }

            return $this->_persistentData->isEnabled()
                && $this->_persistentData->isRememberMeEnabled()
                && $this->_persistentData->isRememberMeCheckedDefault();
        }

        return (bool)$this->_isRememberMeChecked;
    }

    /**
     * Set "Remember Me" checked or not
     *
     * @param bool $checked
     */
    public function setRememberMeChecked($checked = true)
    {
        $this->_isRememberMeChecked = $checked;
    }

    /**
     * Return persistent customer
     *
     * @return Magento_Customer_Model_Customer|bool
     */
    public function getCustomer()
    {
        if (is_null($this->_customer)) {
            $customerId = $this->getSession()->getCustomerId();
            $this->_customer = Mage::getModel('Magento_Customer_Model_Customer')->load($customerId);
        }
        return $this->_customer;
    }
}
