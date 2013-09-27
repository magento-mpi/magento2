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
 */
class Magento_Persistent_Helper_Session extends Magento_Core_Helper_Data
{
    /**
     * Instance of Session Model
     *
     * @var Magento_Persistent_Model_Session
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
     * Persistent session factory
     *
     * @var Magento_Persistent_Model_SessionFactory
     */
    protected $_sessionFactory;

    /**
     * Customer factory
     *
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Checkout session
     *
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Persistent_Helper_Data $persistentData
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config $config
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Persistent_Helper_Data $persistentData,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Persistent_Model_SessionFactory $sessionFactory,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Locale $locale,
        Magento_Core_Model_Date $dateModel,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_Config_Resource $configResource
    ) {
        $this->_persistentData = $persistentData;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerFactory = $customerFactory;
        $this->_sessionFactory = $sessionFactory;
        parent::__construct($eventManager, $coreHttp, $context, $config, $coreStoreConfig, $storeManager, $locale, $dateModel, $appState,
            $configResource);
    }

    /**
     * Get Session model
     *
     * @return Magento_Persistent_Model_Session
     */
    public function getSession()
    {
        if (is_null($this->_sessionModel)) {
            $this->_sessionModel = $this->_sessionFactory->create();
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
            $isRememberMeChecked = $this->_checkoutSession->getRememberMeChecked();
            if (!is_null($isRememberMeChecked)) {
                $this->_isRememberMeChecked = $isRememberMeChecked;
                $this->_checkoutSession->unsRememberMeChecked();
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
            $this->_customer = $this->_customerFactory->create()->load($customerId);
        }
        return $this->_customer;
    }
}
