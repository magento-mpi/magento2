<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Helper;

/**
 * Persistent Shopping Cart Data Helper
 */
class Session extends \Magento\Core\Helper\Data
{
    /**
     * Instance of Session Model
     *
     * @var \Magento\Persistent\Model\Session
     */
    protected $_sessionModel;

    /**
     * Persistent customer
     *
     * @var null|\Magento\Customer\Model\Customer
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
     * @var \Magento\Persistent\Helper\Data
     */
    protected $_persistentData;

    /**
     * Persistent session factory
     *
     * @var \Magento\Persistent\Model\SessionFactory
     */
    protected $_sessionFactory;

    /**
     * Customer factory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Store\Model\Store\Config $coreStoreConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\State $appState
     * @param Data $persistentData
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Persistent\Model\SessionFactory $sessionFactory
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Store\Model\Store\Config $coreStoreConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\App\State $appState,
        \Magento\Persistent\Helper\Data $persistentData,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Persistent\Model\SessionFactory $sessionFactory,
        $dbCompatibleMode = true
    ) {
        $this->_persistentData = $persistentData;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerFactory = $customerFactory;
        $this->_sessionFactory = $sessionFactory;
        parent::__construct(
            $context,
            $coreStoreConfig,
            $storeManager,
            $appState,
            $dbCompatibleMode
        );
    }

    /**
     * Get Session model
     *
     * @return \Magento\Persistent\Model\Session
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
     * @param \Magento\Persistent\Model\Session $sessionModel
     * @return \Magento\Persistent\Model\Session
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
     * @return void
     */
    public function setRememberMeChecked($checked = true)
    {
        $this->_isRememberMeChecked = $checked;
    }

    /**
     * Return persistent customer
     *
     * @return \Magento\Customer\Model\Customer|bool
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
