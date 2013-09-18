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
 * Persistent Observer
 *
 * @category   Magento
 * @package    Magento_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Persistent\Model;

class Observer
{
    /**
     * Whether set quote to be persistent in workflow
     *
     * @var bool
     */
    protected $_setQuotePersistent = true;

    /**
     * Persistent data
     *
     * @var \Magento\Persistent\Helper\Data
     */
    protected $_persistentData = null;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * Persistent session
     *
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSession = null;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager = null;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Persistent\Helper\Session $persistentSession
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Persistent\Helper\Data $persistentData
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Persistent\Helper\Session $persistentSession,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Persistent\Helper\Data $persistentData
    ) {
        $this->_eventManager = $eventManager;
        $this->_persistentSession = $persistentSession;
        $this->_coreData = $coreData;
        $this->_persistentData = $persistentData;
    }

    /**
     * Apply persistent data
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Persistent\Model\Observer
     */
    public function applyPersistentData($observer)
    {
        if (!$this->_persistentData->canProcess($observer)
            || !$this->_persistentSession->isPersistent()
            || \Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()
        ) {
            return $this;
        }
        \Mage::getModel('Magento\Persistent\Model\Persistent\Config')
            ->setConfigFilePath($this->_persistentData->getPersistentConfigFilePath())
            ->fire();
        return $this;
    }

    /**
     * Apply persistent data to specific block
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Persistent\Model\Observer
     */
    public function applyBlockPersistentData($observer)
    {
        if (!$this->_persistentSession->isPersistent() || \Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            return $this;
        }

        /** @var $block \Magento\Core\Block\AbstractBlock */
        $block = $observer->getEvent()->getBlock();

        if (!$block) {
            return $this;
        }

        $xPath = '//instances/blocks/*[block_type="' . get_class($block) . '"]';
        $configFilePath = $observer->getEvent()->getConfigFilePath();
        if (!$configFilePath) {
            $configFilePath = $this->_persistentData->getPersistentConfigFilePath();
        }

        /** @var $persistentConfig \Magento\Persistent\Model\Persistent\Config */
        $persistentConfig = \Mage::getModel('Magento\Persistent\Model\Persistent\Config')
            ->setConfigFilePath($configFilePath);

        foreach ($persistentConfig->getXmlConfig()->xpath($xPath) as $persistentConfigInfo) {
            $persistentConfig->fireOne($persistentConfigInfo->asArray(), $block);
        }

        return $this;
    }

    /**
     * Emulate 'welcome' block with persistent data
     *
     * @param \Magento\Core\Block\AbstractBlock $block
     * @return \Magento\Persistent\Model\Observer
     */
    public function emulateWelcomeBlock($block)
    {
        $escapedName = $this->_coreData
            ->escapeHtml($this->_getPersistentCustomer()->getName(), null);

        $this->_applyAccountLinksPersistentData();
        $welcomeMessage = __('Welcome, %1!', $escapedName)
            . ' ' . \Mage::app()->getLayout()->getBlock('header.additional')->toHtml();
        $block->setWelcome($welcomeMessage);
        return $this;
    }

    /**
     * Emulate 'account links' block with persistent data
     */
    protected function _applyAccountLinksPersistentData()
    {
        if (!\Mage::app()->getLayout()->getBlock('header.additional')) {
            \Mage::app()->getLayout()->addBlock('Magento\Persistent\Block\Header\Additional', 'header.additional');
        }
    }

    /**
     * Emulate 'top links' block with persistent data
     *
     * @param \Magento\Core\Block\AbstractBlock $block
     */
    public function emulateTopLinks($block)
    {
        $this->_applyAccountLinksPersistentData();
        $block->removeLinkByUrl(\Mage::getUrl('customer/account/login'));
    }

    /**
     * Emulate quote by persistent data
     *
     * @param \Magento\Event\Observer $observer
     */
    public function emulateQuote($observer)
    {
        $stopActions = array(
            'persistent_index_saveMethod',
            'customer_account_createpost'
        );

        if (!$this->_persistentData->canProcess($observer)
            || !$this->_persistentSession->isPersistent()
            || \Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()
        ) {
            return;
        }

        /** @var $action \Magento\Checkout\Controller\Onepage */
        $action = $observer->getEvent()->getControllerAction();
        $actionName = $action->getFullActionName();

        if (in_array($actionName, $stopActions)) {
            return;
        }

        /** @var $checkoutSession \Magento\Checkout\Model\Session */
        $checkoutSession = \Mage::getSingleton('Magento\Checkout\Model\Session');
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
     * @param \Magento\Event\Observer $observer
     */
    public function setQuotePersistentData($observer)
    {
        if (!$this->_isPersistent()) {
            return;
        }

        /** @var $quote \Magento\Sales\Model\Quote */
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
     * @param \Magento\Event\Observer $observer
     */
    public function setLoadPersistentQuote($observer)
    {
        if (!$this->_isGuestShoppingCart()) {
            return;
        }

        /** @var $checkoutSession \Magento\Checkout\Model\Session */
        $checkoutSession = $observer->getEvent()->getCheckoutSession();
        if ($checkoutSession) {
            $checkoutSession->setLoadInactive();
        }
    }

    /**
     * Prevent clear checkout session
     *
     * @param \Magento\Event\Observer $observer
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
     * @param \Magento\Event\Observer $observer
     */
    public function makePersistentQuoteGuest($observer)
    {
        if (!$this->_checkClearCheckoutSessionNecessity($observer)) {
            return;
        }

        $this->setQuoteGuest(true);
    }

    /**
     * Check if checkout session should NOT be cleared
     *
     * @param \Magento\Event\Observer $observer
     * @return bool|\Magento\Persistent\Controller\Index
     */
    protected function _checkClearCheckoutSessionNecessity($observer)
    {
        if (!$this->_isGuestShoppingCart()) {
            return false;
        }

        /** @var $action \Magento\Persistent\Controller\Index */
        $action = $observer->getEvent()->getControllerAction();
        if ($action instanceof \Magento\Persistent\Controller\Index) {
            return $action;
        }

        return false;
    }

    /**
     * Reset session data when customer re-authenticates
     *
     * @param \Magento\Event\Observer $observer
     */
    public function customerAuthenticatedEvent($observer)
    {
        /** @var $customerSession \Magento\Customer\Model\Session */
        $customerSession = \Mage::getSingleton('Magento\Customer\Model\Session');
        $customerSession->setCustomerId(null)->setCustomerGroupId(null);

        if (\Mage::app()->getRequest()->getParam('context') != 'checkout') {
            $this->_expirePersistentSession();
            return;
        }

        $this->setQuoteGuest();
    }

    /**
     * Unset persistent cookie and make customer's quote as a guest
     *
     * @param \Magento\Event\Observer $observer
     */
    public function removePersistentCookie($observer)
    {
        if (!$this->_persistentData->canProcess($observer) || !$this->_isPersistent()) {
            return;
        }

        $this->_persistentSession->getSession()->removePersistentCookie();
        /** @var $customerSession \Magento\Customer\Model\Session */
        $customerSession = \Mage::getSingleton('Magento\Customer\Model\Session');
        if (!$customerSession->isLoggedIn()) {
            $customerSession->setCustomerId(null)->setCustomerGroupId(null);
        }

        $this->setQuoteGuest();
    }

    /**
     * Disable guest checkout if we are in persistent mode
     *
     * @param \Magento\Event\Observer $observer
     */
    public function disableGuestCheckout($observer)
    {
        if ($this->_persistentSession->isPersistent()) {
            $observer->getEvent()->getResult()->setIsAllowed(false);
        }
    }

    /**
     * Prevent express checkout with Google checkout and PayPal Express checkout
     *
     * @param \Magento\Event\Observer $observer
     */
    public function preventExpressCheckout($observer)
    {
        if (!$this->_isLoggedOut()) {
            return;
        }

        /** @var $controllerAction \Magento\Core\Controller\Front\Action */
        $controllerAction = $observer->getEvent()->getControllerAction();
        if (method_exists($controllerAction, 'redirectLogin')) {
            \Mage::getSingleton('Magento\Core\Model\Session')->addNotice(
                __('To check out, please log in using your email address.')
            );
            $controllerAction->redirectLogin();
            if ($controllerAction instanceof \Magento\GoogleCheckout\Controller\Redirect
                || $controllerAction instanceof \Magento\Paypal\Controller\Express\AbstractExpress
            ) {
                \Mage::getSingleton('Magento\Customer\Model\Session')
                    ->setBeforeAuthUrl(\Mage::getUrl('persistent/index/expressCheckout'));
            }
        }
    }

    /**
     * Retrieve persistent customer instance
     *
     * @return \Magento\Customer\Model\Customer
     */
    protected function _getPersistentCustomer()
    {
        return \Mage::getModel('Magento\Customer\Model\Session')->load(
            $this->_persistentSession->getSession()->getCustomerId()
        );
    }

    /**
     * Return current active quote for persistent customer
     *
     * @return \Magento\Sales\Model\Quote
     */
    protected function _getQuote()
    {
        $quote = \Mage::getModel('Magento\Sales\Model\Quote');
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
        return $this->_persistentData->isShoppingCartPersist();
    }

    /**
     * Check whether persistent mode is running
     *
     * @return bool
     */
    protected function _isPersistent()
    {
        return $this->_persistentSession->isPersistent();
    }

    /**
     * Check if persistent mode is running and customer is logged out
     *
     * @return bool
     */
    protected function _isLoggedOut()
    {
        return $this->_isPersistent() && !\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn();
    }

    /**
     * Check if shopping cart is guest while persistent session and user is logged out
     *
     * @return bool
     */
    protected function _isGuestShoppingCart()
    {
        return $this->_isLoggedOut() && !$this->_persistentData->isShoppingCartPersist();
    }

    /**
     * Make quote to be guest
     *
     * @param bool $checkQuote Check quote to be persistent (not stolen)
     */
    public function setQuoteGuest($checkQuote = false)
    {
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
        if ($quote && $quote->getId()) {
            if ($checkQuote
                && !$this->_persistentData->isShoppingCartPersist()
                && !$quote->getIsPersistent()
            ) {
                \Mage::getSingleton('Magento\Checkout\Model\Session')->unsetAll();
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
                ->setCustomerGroupId(\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID)
                ->setIsPersistent(false)
                ->removeAllAddresses();
            //Create guest addresses
            $quote->getShippingAddress();
            $quote->getBillingAddress();
            $quote->collectTotals()->save();
        }

        $this->_persistentSession->getSession()->removePersistentCookie();
    }

    /**
     * Check and clear session data if persistent session expired
     *
     * @param \Magento\Event\Observer $observer
     */
    public function checkExpirePersistentQuote(\Magento\Event\Observer $observer)
    {
        if (!$this->_persistentData->canProcess($observer)) {
            return;
        }

        /** @var $customerSession \Magento\Customer\Model\Session */
        $customerSession = \Mage::getSingleton('Magento\Customer\Model\Session');

        if ($this->_persistentData->isEnabled()
            && !$this->_isPersistent()
            && !$customerSession->isLoggedIn()
            && \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuoteId()
            && !($observer->getControllerAction() instanceof \Magento\Checkout\Controller\Onepage)
            // persistent session does not expire on onepage checkout page to not spoil customer group id
        ) {
            $this->_eventManager->dispatch('persistent_session_expired');
            $this->_expirePersistentSession();
            $customerSession->setCustomerId(null)->setCustomerGroupId(null);
        }
    }

    protected function _expirePersistentSession()
    {
        /** @var $checkoutSession \Magento\Checkout\Model\Session */
        $checkoutSession = \Mage::getSingleton('Magento\Checkout\Model\Session');

        $quote = $checkoutSession->setLoadInactive()->getQuote();
        if ($quote->getIsActive() && $quote->getCustomerId()) {
            $checkoutSession->setCustomer(null)->unsetAll();
        } else {
            $quote
                ->setIsActive(true)
                ->setIsPersistent(false)
                ->setCustomerId(null)
                ->setCustomerGroupId(\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID);
        }
    }

    /**
     * Clear expired persistent sessions
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @return \Magento\Persistent\Model\Observer\Cron
     */
    public function clearExpiredCronJob(\Magento\Cron\Model\Schedule $schedule)
    {
        $websiteIds = \Mage::getResourceModel('Magento\Core\Model\Resource\Website\Collection')->getAllIds();
        if (!is_array($websiteIds)) {
            return $this;
        }

        foreach ($websiteIds as $websiteId) {
            \Mage::getModel('Magento\Persistent\Model\Session')->deleteExpired($websiteId);
        }

        return $this;
    }

    /**
     * Update customer id and customer group id if user is in persistent session
     *
     * @param \Magento\Event\Observer $observer
     */
    public function updateCustomerCookies(\Magento\Event\Observer $observer)
    {
        if (!$this->_isPersistent()) {
            return;
        }

        $customerCookies = $observer->getEvent()->getCustomerCookies();
        if ($customerCookies instanceof \Magento\Object) {
            $persistentCustomer = $this->_getPersistentCustomer();
            $customerCookies->setCustomerId($persistentCustomer->getId());
            $customerCookies->setCustomerGroupId($persistentCustomer->getGroupId());
        }
    }

    /**
     * Set persistent data to customer session
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Persistent\Model\Observer
     */
    public function emulateCustomer($observer)
    {
        if (!$this->_persistentData->canProcess($observer)
            || !$this->_isShoppingCartPersist()
        ) {
            return $this;
        }

        if ($this->_isLoggedOut()) {
            /** @var $customer \Magento\Customer\Model\Session */
            $customer = \Mage::getModel('Magento\Customer\Model\Session')->load(
                $this->_persistentSession->getSession()->getCustomerId()
            );
            \Mage::getSingleton('Magento\Customer\Model\Session')
                ->setCustomerId($customer->getId())
                ->setCustomerGroupId($customer->getGroupId());
        }
        return $this;
    }
}
