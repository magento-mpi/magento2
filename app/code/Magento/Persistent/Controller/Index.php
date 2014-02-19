<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Controller;

/**
 * Persistent front controller
 */
class Index extends \Magento\App\Action\Action
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
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Persistent observer
     *
     * @var \Magento\Persistent\Model\Observer
     */
    protected $_persistentObserver;

    /**
     * Construct
     *
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Persistent\Model\Observer $persistentObserver
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Persistent\Model\Observer $persistentObserver,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_persistentObserver = $persistentObserver;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Set whether clear checkout session when logout
     *
     * @param bool $clear
     * @return $this
     */
    public function setClearCheckoutSession($clear = true)
    {
        $this->_clearCheckoutSession = $clear;
        return $this;
    }

    /**
     * Retrieve 'persistent session' helper instance
     *
     * @return \Magento\Persistent\Helper\Session
     */
    protected function _getHelper()
    {
        return $this->_objectManager->get('Magento\Persistent\Helper\Session');
    }

    /**
     * Unset persistent cookie action
     *
     * @return void
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
     * @return $this
     */
    protected function _cleanup()
    {
        $this->_eventManager->dispatch('persistent_session_expired');
        $this->_customerSession
            ->setCustomerId(null)
            ->setCustomerGroupId(null);
        if ($this->_clearCheckoutSession) {
            $this->_checkoutSession->clearStorage();
        }
        $this->_getHelper()->getSession()->removePersistentCookie();
        return $this;
    }

    /**
     * Save onepage checkout method to be register
     *
     * @return void
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

        $checkoutUrl = $this->_redirect->getRefererUrl();
        $this->getResponse()->setRedirect($checkoutUrl . (strpos($checkoutUrl, '?') ? '&' : '?') . 'register');
    }

    /**
     * Add appropriate session message and redirect to shopping cart
     * used for google checkout and paypal express checkout
     *
     * @return void
     */
    public function expressCheckoutAction()
    {
        $this->messageManager->addNotice(__('Your shopping cart has been updated with new prices.'));
        $this->_redirect('checkout/cart');
    }
}
