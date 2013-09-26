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
 * Persistent Session Observer
 */
class Magento_Persistent_Model_Observer_Session
{
    /**
     * Create/Update and Load session when customer log in
     *
     * @param Magento_Event_Observer $observer
     */
    /**
     * Persistent session
     *
     * @var Magento_Persistent_Helper_Session
     */
    protected $_persistentSession = null;

    /**
     * Persistent data
     *
     * @var Magento_Persistent_Helper_Data
     */
    protected $_persistentData = null;

    /**
     * Session factory
     *
     * @var Magento_Persistent_Model_SessionFactory
     */
    protected $_sessionFactory;

    /**
     * Cookie model
     *
     * @var Magento_Core_Model_Cookie
     */
    protected $_cookie;

    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Checkout session
     *
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * Construct
     *
     * @param Magento_Persistent_Helper_Data $persistentData
     * @param Magento_Persistent_Helper_Session $persistentSession
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Model_Cookie $cookie
     * @param Magento_Persistent_Model_SessionFactory $sessionFactory
     */
    public function __construct(
        Magento_Persistent_Helper_Data $persistentData,
        Magento_Persistent_Helper_Session $persistentSession,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_Cookie $cookie,
        Magento_Persistent_Model_SessionFactory $sessionFactory
    ) {
        $this->_persistentData = $persistentData;
        $this->_persistentSession = $persistentSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_cookie = $cookie;
        $this->_sessionFactory = $sessionFactory;
    }

    public function synchronizePersistentOnLogin(Magento_Event_Observer $observer)
    {
        /** @var $customer Magento_Customer_Model_Customer */
        $customer = $observer->getEvent()->getCustomer();
        // Check if customer is valid (remove persistent cookie for invalid customer)
        if (!$customer
            || !$customer->getId()
            || !$this->_persistentSession->isRememberMeChecked()
        ) {
            $this->_sessionFactory->create()->removePersistentCookie();
            return;
        }

        $persistentLifeTime = $this->_persistentData->getLifeTime();
        // Delete persistent session, if persistent could not be applied
        if ($this->_persistentData->isEnabled() && ($persistentLifeTime <= 0)) {
            // Remove current customer persistent session
            $this->_sessionFactory->create()->deleteByCustomerId($customer->getId());
            return;
        }

        /** @var $sessionModel Magento_Persistent_Model_Session */
        $sessionModel = $this->_persistentSession->getSession();

        // Check if session is wrong or not exists, so create new session
        if (!$sessionModel->getId() || ($sessionModel->getCustomerId() != $customer->getId())) {
            /** @var Magento_Persistent_Model_Session $sessionModel */
            $sessionModel = $this->_sessionFactory->create();
            $sessionModel->setLoadExpired()
                ->loadByCustomerId($customer->getId());
            if (!$sessionModel->getId()) {
                /** @var Magento_Persistent_Model_Session $sessionModel */
                $sessionModel = $this->_sessionFactory->create();
                $sessionModel->setCustomerId($customer->getId())
                    ->save();
            }

            $this->_persistentSession->setSession($sessionModel);
        }

        // Set new cookie
        if ($sessionModel->getId()) {
            $this->_cookie->set(
                Magento_Persistent_Model_Session::COOKIE_NAME,
                $sessionModel->getKey(),
                $persistentLifeTime
            );
        }
    }

    /**
     * Unload persistent session (if set in config)
     *
     * @param Magento_Event_Observer $observer
     */
    public function synchronizePersistentOnLogout(Magento_Event_Observer $observer)
    {
        if (!$this->_persistentData->isEnabled() || !$this->_persistentData->getClearOnLogout()) {
            return;
        }

        /** @var $customer Magento_Customer_Model_Customer */
        $customer = $observer->getEvent()->getCustomer();
        // Check if customer is valid
        if (!$customer || !$customer->getId()) {
            return;
        }

        $this->_sessionFactory->create()->removePersistentCookie();

        // Unset persistent session
        $this->_persistentSession->setSession(null);
    }

    /**
     * Synchronize persistent session info
     *
     * @param Magento_Event_Observer $observer
     */
    public function synchronizePersistentInfo(Magento_Event_Observer $observer)
    {
        if (!$this->_persistentData->isEnabled()
            || !$this->_persistentSession->isPersistent()
        ) {
            return;
        }

        /** @var $sessionModel Magento_Persistent_Model_Session */
        $sessionModel = $this->_persistentSession->getSession();

        /** @var $request Magento_Core_Controller_Request_Http */
        $request = $observer->getEvent()->getFront()->getRequest();

        // Quote Id could be changed only by logged in customer
        if ($this->_customerSession->isLoggedIn()
            || ($request && $request->getActionName() == 'logout' && $request->getControllerName() == 'account')
        ) {
            $sessionModel->save();
        }
    }

    /**
     * Set Checked status of "Remember Me"
     *
     * @param Magento_Event_Observer $observer
     */
    public function setRememberMeCheckedStatus(Magento_Event_Observer $observer)
    {
        if (!$this->_persistentData->canProcess($observer)
            || !$this->_persistentData->isEnabled()
            || !$this->_persistentData->isRememberMeEnabled()
        ) {
            return;
        }

        /** @var $controllerAction Magento_Core_Controller_Varien_Action */
        $controllerAction = $observer->getEvent()->getControllerAction();
        if ($controllerAction) {
            $rememberMeCheckbox = $controllerAction->getRequest()->getPost('persistent_remember_me');
            $this->_persistentSession->setRememberMeChecked((bool)$rememberMeCheckbox);
            if (
                $controllerAction->getFullActionName() == 'checkout_onepage_saveBilling'
                    || $controllerAction->getFullActionName() == 'customer_account_createpost'
            ) {
                $this->_checkoutSession->setRememberMeChecked((bool)$rememberMeCheckbox);
            }
        }
    }

    /**
     * Renew persistent cookie
     *
     * @param Magento_Event_Observer $observer
     */
    public function renewCookie(Magento_Event_Observer $observer)
    {
        if (!$this->_persistentData->canProcess($observer)
            || !$this->_persistentData->isEnabled()
            || !$this->_persistentSession->isPersistent()
        ) {
            return;
        }

        /** @var $controllerAction Magento_Core_Controller_Front_Action */
        $controllerAction = $observer->getEvent()->getControllerAction();

        if ($this->_customerSession->isLoggedIn()
            || $controllerAction->getFullActionName() == 'customer_account_logout'
        ) {
            $this->_cookie->renew(
                Magento_Persistent_Model_Session::COOKIE_NAME,
                $this->_persistentData->getLifeTime()
            );
        }
    }
}
