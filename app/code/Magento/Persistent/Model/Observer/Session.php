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
    public function synchronizePersistentOnLogin(Magento_Event_Observer $observer)
    {
        /** @var $customer Magento_Customer_Model_Customer */
        $customer = $observer->getEvent()->getCustomer();
        // Check if customer is valid (remove persistent cookie for invalid customer)
        if (!$customer
            || !$customer->getId()
            || !Mage::helper('Magento_Persistent_Helper_Session')->isRememberMeChecked()
        ) {
            Mage::getModel('Magento_Persistent_Model_Session')->removePersistentCookie();
            return;
        }

        $persistentLifeTime = Mage::helper('Magento_Persistent_Helper_Data')->getLifeTime();
        // Delete persistent session, if persistent could not be applied
        if (Mage::helper('Magento_Persistent_Helper_Data')->isEnabled() && ($persistentLifeTime <= 0)) {
            // Remove current customer persistent session
            Mage::getModel('Magento_Persistent_Model_Session')->deleteByCustomerId($customer->getId());
            return;
        }

        /** @var $sessionModel Magento_Persistent_Model_Session */
        $sessionModel = Mage::helper('Magento_Persistent_Helper_Session')->getSession();

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

            Mage::helper('Magento_Persistent_Helper_Session')->setSession($sessionModel);
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
        $helper = Mage::helper('Magento_Persistent_Helper_Data');
        if (!$helper->isEnabled() || !$helper->getClearOnLogout()) {
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
        Mage::helper('Magento_Persistent_Helper_Session')->setSession(null);
    }

    /**
     * Synchronize persistent session info
     *
     * @param Magento_Event_Observer $observer
     */
    public function synchronizePersistentInfo(Magento_Event_Observer $observer)
    {
        $helper = Mage::helper('Magento_Persistent_Helper_Session');
        if (!Mage::helper('Magento_Persistent_Helper_Data')->isEnabled()
            || !$helper->isPersistent()
        ) {
            return;
        }

        /** @var $sessionModel Magento_Persistent_Model_Session */
        $sessionModel = $helper->getSession();

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
        $helper = Mage::helper('Magento_Persistent_Helper_Data');
        if (!$helper->canProcess($observer)
            || !$helper->isEnabled()
            || !$helper->isRememberMeEnabled()
        ) {
            return;
        }

        /** @var $controllerAction Magento_Core_Controller_Varien_Action */
        $controllerAction = $observer->getEvent()->getControllerAction();
        if ($controllerAction) {
            $rememberMeCheckbox = $controllerAction->getRequest()->getPost('persistent_remember_me');
            Mage::helper('Magento_Persistent_Helper_Session')->setRememberMeChecked((bool)$rememberMeCheckbox);
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
        $helper = Mage::helper('Magento_Persistent_Helper_Data');
        if (!$helper->canProcess($observer)
            || !$helper->isEnabled()
            || !Mage::helper('Magento_Persistent_Helper_Session')->isPersistent()
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
                $helper->getLifeTime()
            );
        }
    }
}
