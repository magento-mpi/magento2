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
 * Persistent front controller
 */
class Magento_Persistent_Controller_Index extends Magento_Core_Controller_Front_Action
{
    /**
     * Whether clear checkout session when logout
     *
     * @var bool
     */
    protected $_clearCheckoutSession = true;

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
     * Persistent observer
     *
     * @var Magento_Persistent_Model_Observer
     */
    protected $_persistentObserver;

    /**
     * Core session model
     *
     * @var Magento_Core_Model_Session
     */
    protected $_session;

    /**
     * Construct
     *
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Session $session
     * @param Magento_Persistent_Model_Observer $persistentObserver
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Customer_Model_Session $customerSession
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Session $session,
        Magento_Persistent_Model_Observer $persistentObserver,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Customer_Model_Session $customerSession
    ) {
        $this->_session = $session;
        $this->_persistentObserver = $persistentObserver;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Set whether clear checkout session when logout
     *
     * @param bool $clear
     * @return Magento_Persistent_Controller_Index
     */
    public function setClearCheckoutSession($clear = true)
    {
        $this->_clearCheckoutSession = $clear;
        return $this;
    }

    /**
     * Retrieve 'persistent session' helper instance
     *
     * @return Magento_Persistent_Helper_Session
     */
    protected function _getHelper()
    {
        return $this->_objectManager->get('Magento_Persistent_Helper_Session');
    }

    /**
     * Unset persistent cookie action
     */
    public function unsetCookieAction()
    {
        if ($this->_getHelper()->isPersistent()) {
            $this->_cleanup();
        }
        $this->_redirect('customer/account/login');
        return;
    }

    /**
     * Revert all persistent data
     *
     * @return Magento_Persistent_Controller_Index
     */
    protected function _cleanup()
    {
        $this->_eventManager->dispatch('persistent_session_expired');
        $this->_customerSession
            ->setCustomerId(null)
            ->setCustomerGroupId(null);
        if ($this->_clearCheckoutSession) {
            $this->_checkoutSession->unsetAll();
        }
        $this->_getHelper()->getSession()->removePersistentCookie();
        return $this;
    }

    /**
     * Save onepage checkout method to be register
     */
    public function saveMethodAction()
    {
        if ($this->_getHelper()->isPersistent()) {
            $this->_getHelper()->getSession()->removePersistentCookie();
            if (!$this->_customerSession->isLoggedIn()) {
                $this->_customerSession->setCustomerId(null)
                    ->setCustomerGroupId(null);
            }

            $this->_persistentObserver->setQuoteGuest();
        }

        $checkoutUrl = $this->_getRefererUrl();
        $this->_redirectUrl($checkoutUrl . (strpos($checkoutUrl, '?') ? '&' : '?') . 'register');
    }

    /**
     * Add appropriate session message and redirect to shopping cart
     * used for google checkout and paypal express checkout
     */
    public function expressCheckoutAction()
    {
        $this->_session->addNotice(__('Your shopping cart has been updated with new prices.'));
        $this->_redirect('checkout/cart');
    }
}
