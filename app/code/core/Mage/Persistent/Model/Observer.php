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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Observer
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Model_Observer
{
    /**
     * Whether set quote to be persistent in workflow
     *
     * @var bool
     */
    protected $_setQuotePersistent = true;

    /**
     * Apply persistent data
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Persistent_Model_Observer
     */
    public function applyPersistentData($observer)
    {
        if (!$this->_getPersistentHelper()->isPersistent() || Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this;
        }
        Mage::getModel('persistent/persistent_config')
            ->setConfigFilePath(Mage::helper('persistent')->getPersistentConfigFilePath())
            ->fire();
        return $this;
    }

    /**
     * Emulate 'welcome' block with persistent data
     *
     * @param Mage_Core_Block_Abstract $block
     * @return Mage_Persistent_Model_Observer
     */
    public function emulateWelcomeBlock($block)
    {
        $block->setWelcome(
            Mage::helper('persistent')->__('Welcome, %s!', Mage::helper('core')->escapeHtml($this->_getPersistentCustomer()->getName(), null))
        );

        $additionalBlock = $block->getLayout()->getBlock('header.additional');
        if ($additionalBlock) {
            $block->setAdditionalHtml($additionalBlock->toHtml());
        }
        $block->getLayout()->getBlock('top.links')->removeLinkByUrl(Mage::getUrl('customer/account/login'));

        return $this;
    }

    /**
     * Emulate quote by persistent data
     *
     * @param Varien_Event_Observer $observer
     */
    public function emulateQuote($observer)
    {
        $stopActions = array(
            'checkout_onepage_saveMethod',
            'customer_account_createpost'
        );

        if (!$this->_getPersistentHelper()->isPersistent() || Mage::getSingleton('customer/session')->isLoggedIn()) {
            return;
        }

        /** @var $action Mage_Checkout_OnepageController */
        $action = $observer->getEvent()->getControllerAction();
        $actionName = $action->getFullActionName();

        if (in_array($actionName, $stopActions)) {
            return;
        }

        /** @var $checkoutSession Mage_Checkout_Model_Session */
        $checkoutSession = Mage::getSingleton('checkout/session');
        if ($this->_isShoppingCartPersist()) {
            $checkoutSession->setCustomer($this->_getPersistentCustomer());
            if (!$checkoutSession->hasQuote()) {
                $checkoutSession->getQuote();
            }
        }
    }

    /**
     * Set persistent data into quote
     *
     * @param Varien_Event_Observer $observer
     */
    public function setQuotePersistentData($observer)
    {
        if (!$this->_isPersistent()) {
            return;
        }

        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return;
        }

        if ($this->_isGuestShoppingCart() && $this->_setQuotePersistent) {
            //Quote is not actual customer's quote, just persistent
            $quote->setIsActive(false)->setIsPersistent(true);
        }
    }

    /**
     * Set quote to be loaded even if not active
     *
     * @param Varien_Event_Observer $observer
     */
    public function setLoadPersistentQuote($observer)
    {
        if (!$this->_isGuestShoppingCart()) {
            return;
        }

        /** @var $checkoutSession Mage_Checkout_Model_Session */
        $checkoutSession = $observer->getEvent()->getCheckoutSession();
        if ($checkoutSession) {
            $checkoutSession->setLoadInactive();
        }
    }

    /**
     * Prevent clear checkout session
     *
     * @param Varien_Event_Observer $observer
     */
    public function preventClearCheckoutSession($observer)
    {
        $action = $this->_checkClearCheckoutSessionNecessity($observer);

        if ($action) {
            $action->setClearCheckoutSession(false);
        }
    }

    /**
     * Make persistent quote to be guest
     *
     * @param Varien_Event_Observer $observer
     */
    public function makePersistentQuoteGuest($observer)
    {
        if (!$this->_checkClearCheckoutSessionNecessity($observer)) {
            return;
        }

        $this->_setQuoteGuest(true);
    }

    /**
     * Check if checkout session should NOT be cleared
     *
     * @param Varien_Event_Observer $observer
     * @return bool|Mage_Persistent_IndexController
     */
    protected function _checkClearCheckoutSessionNecessity($observer)
    {
        if (!$this->_isGuestShoppingCart()) {
            return false;
        }

        /** @var $action Mage_Persistent_IndexController */
        $action = $observer->getEvent()->getControllerAction();
        if ($action instanceof Mage_Persistent_IndexController) {
            return $action;
        }

        return false;
    }

    /**
     * Reset session data when customer re-authenticates
     *
     * @param Varien_Event_Observer $observer
     */
    public function customerAuthenticatedEvent($observer)
    {
        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('customer/session');
        $customerSession->setCustomerId(null)
            ->setCustomerGroupId(null);

        $this->_setQuoteGuest();
    }

    /**
     * Unset persistent cookie and make customer's quote as a guest
     *
     * @param Varien_Event_Observer $observer
     */
    public function removePersistentCookie($observer)
    {
        if (!$this->_isPersistent()) {
            return;
        }

        $this->_getPersistentHelper()->getSession()->removePersistentCookie();
        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn()) {
            $customerSession->setCustomerId(null)
                ->setCustomerGroupId(null);
        }

        $this->_setQuoteGuest();
    }

    /**
     * Disable guest checkout if we are in persistent mode
     *
     * @param Varien_Event_Observer $observer
     */
    public function disableGuestCheckout($observer)
    {
        if ($this->_getPersistentHelper()->isPersistent()) {
            $observer->getEvent()->getResult()->setIsAllowed(false);
        }
    }

    /**
     * Prevent express checkout with Google checkout and PayPal Express checkout
     *
     * @param Varien_Event_Observer $observer
     */
    public function preventExpressCheckout($observer)
    {
        if (!$this->_isLoggedOut()) {
            return;
        }

        /** @var $controllerAction Mage_Core_Controller_Front_Action */
        $controllerAction = $observer->getEvent()->getControllerAction();
        if (is_callable(array($controllerAction, 'redirectLogin'))) {
            $controllerAction->redirectLogin();
        }
    }

    /**
     * Retrieve persistent customer instance
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getPersistentCustomer()
    {
        return Mage::getModel('customer/customer')->load(
            $this->_getPersistentHelper()->getSession()->getCustomerId()
        );
    }

    /**
     * Retrieve persistent helper
     *
     * @return Mage_Persistent_Helper_Session
     */
    protected function _getPersistentHelper()
    {
        return Mage::helper('persistent/session');
    }

    /**
     * Return current active quote for persistent customer
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        $quote = Mage::getModel('sales/quote');
        $quote->loadByCustomer($this->_getPersistentCustomer());
        return $quote;
    }

    /**
     * Check whether shopping cart is persistent
     *
     * @return bool
     */
    protected function _isShoppingCartPersist()
    {
        return Mage::helper('persistent')->isShoppingCartPersist();
    }

    /**
     * Check whether persistent mode is running
     *
     * @return bool
     */
    protected function _isPersistent()
    {
        return $this->_getPersistentHelper()->isPersistent();
    }

    /**
     * Check if persistent mode is running and customer is logged out
     *
     * @return bool
     */
    protected function _isLoggedOut()
    {
        return $this->_isPersistent() && !Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * Check if shopping cart is guest while persistent session and user is logged out
     *
     * @return bool
     */
    protected function _isGuestShoppingCart()
    {
        return $this->_isLoggedOut() && !Mage::helper('persistent')->isShoppingCartPersist();
    }

    /**
     * Make quote to be guest
     *
     * @param bool $checkQuote Check quote to be persistent (not stolen)
     */
    protected function _setQuoteGuest($checkQuote = false)
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote && $quote->getId()) {
            if ($checkQuote && !Mage::helper('persistent')->isShoppingCartPersist() && !$quote->getIsPersistent()) {
                Mage::getSingleton('checkout/session')->unsetAll();
                return;
            }

            $quote->getPaymentsCollection()->walk('delete');
            $quote->getAddressesCollection()->walk('delete');
            $this->_setQuotePersistent = false;
            $quote
                ->setIsActive(true)
                ->setCustomerId(null)
                ->setCustomerEmail(null)
                ->setCustomerFirstname(null)
                ->setCustomerLastname(null)
                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
                ->setIsPersistent(false)
                ->removeAllAddresses();
            //Create guest addresses
            $quote->getShippingAddress();
            $quote->getBillingAddress();
            $quote->collectTotals()->save();
        }

        $this->_getPersistentHelper()->getSession()->removePersistentCookie();
    }

    /**
     * Check and clear session data if persistent session expired
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkExpirePersistentQuote(Varien_Event_Observer $observer)
    {
        /** @var $checkoutSession Mage_Checkout_Model_Session */
        $checkoutSession = Mage::getSingleton('checkout/session');

        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('customer/session');

        if (Mage::helper('persistent')->isEnabled()
            && !$this->_isPersistent()
            && !$customerSession->isLoggedIn()
            && $checkoutSession->getQuoteId()
        ) {
            $quote = $checkoutSession->setLoadInactive()->getQuote();
            if ($quote->getIsActive() && $quote->getCustomerId()) {
                $checkoutSession->unsetAll();
            } else {
                $quote
                    ->setIsActive(true)
                    ->setIsPersistent(false)
                    ->setCustomerId(null)
                    ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
            }
            $customerSession->setCustomerId(null)->setCustomerGroupId(null);
        }
    }

    /**
     * Clear expired persistent sessions
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @return Mage_Persistent_Model_Observer_Cron
     */
    public function clearExpiredCronJob(Mage_Cron_Model_Schedule $schedule)
    {
        $websiteIds = Mage::getResourceModel('core/website_collection')->getAllIds();
        if (!is_array($websiteIds)) {
            return $this;
        }

        foreach ($websiteIds as $websiteId) {
            Mage::getModel('persistent/session')->deleteExpired($websiteId);
        }

        return $this;
    }

    /**
     * Create handle for persistent session if persistent cookie and customer not logged in
     *
     * @param Varien_Event_Observer $observer
     */
    public function createPersistentHandleLayout(Varien_Event_Observer $observer)
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = $observer->getEvent()->getLayout();
        if ($layout && Mage::helper('persistent')->isEnabled()
            && Mage::helper('persistent/session')->isPersistent()
        ) {
            $handle = (Mage::getSingleton('customer/session')->isLoggedIn())
                ? Mage_Persistent_Helper_Data::LOGGED_IN_LAYOUT_HANDLE
                : Mage_Persistent_Helper_Data::LOGGED_OUT_LAYOUT_HANDLE;
            $layout->getUpdate()->addHandle($handle);
        }
    }
}
