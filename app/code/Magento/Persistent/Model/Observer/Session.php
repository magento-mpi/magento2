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
 *
 * @category   Magento
 * @package    Magento_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @param Magento_Persistent_Helper_Data $persistentData
     * @param Magento_Persistent_Helper_Session $persistentSession
     */
    public function __construct(
        Magento_Persistent_Helper_Data $persistentData,
        Magento_Persistent_Helper_Session $persistentSession
    ) {
        $this->_persistentData = $persistentData;
        $this->_persistentSession = $persistentSession;
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
            Mage::getModel('Magento_Persistent_Model_Session')->removePersistentCookie();
            return;
        }

        $persistentLifeTime = $this->_persistentData->getLifeTime();
        // Delete persistent session, if persistent could not be applied
        if ($this->_persistentData->isEnabled() && ($persistentLifeTime <= 0)) {
            // Remove current customer persistent session
            Mage::getModel('Magento_Persistent_Model_Session')->deleteByCustomerId($customer->getId());
            return;
        }

        /** @var $sessionModel Magento_Persistent_Model_Session */
        $sessionModel = $this->_persistentSession->getSession();

        // Check if session is wrong or not exists, so create new session
        if (!$sessionModel->getId() || ($sessionModel->getCustomerId() != $customer->getId())) {
            $sessionModel = Mage::getModel('Magento_Persistent_Model_Session')
                ->setLoadExpired()
                ->loadByCustomerId($customer->getId());
            if (!$sessionModel->getId()) {
                $sessionModel = Mage::getModel('Magento_Persistent_Model_Session')
                    ->setCustomerId($customer->getId())
                    ->save();
            }

            $this->_persistentSession->setSession($sessionModel);
        }

        // Set new cookie
        if ($sessionModel->getId()) {
            Mage::getSingleton('Magento_Core_Model_Cookie')->set(
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

        Mage::getModel('Magento_Persistent_Model_Session')->removePersistentCookie();

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
        if (Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()
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
                Mage::getSingleton('Magento_Checkout_Model_Session')->setRememberMeChecked((bool)$rememberMeCheckbox);
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

        if (Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()
            || $controllerAction->getFullActionName() == 'customer_account_logout'
        ) {
            Mage::getSingleton('Magento_Core_Model_Cookie')->renew(
                Magento_Persistent_Model_Session::COOKIE_NAME,
                $this->_persistentData->getLifeTime()
            );
        }
    }
}
