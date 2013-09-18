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
 *
 * @category   Magento
 * @package    Magento_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Persistent\Controller;

class Index extends \Magento\Core\Controller\Front\Action
{
    /**
     * Whether clear checkout session when logout
     *
     * @var bool
     */
    protected $_clearCheckoutSession = true;

    /**
     * Set whether clear checkout session when logout
     *
     * @param bool $clear
     * @return \Magento\Persistent\Controller\Index
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
     * @return \Magento\Persistent\Controller\Index
     */
    protected function _cleanup()
    {
        $this->_eventManager->dispatch('persistent_session_expired');
        $customerSession = \Mage::getSingleton('Magento\Customer\Model\Session');
        $customerSession
            ->setCustomerId(null)
            ->setCustomerGroupId(null);
        if ($this->_clearCheckoutSession) {
            \Mage::getSingleton('Magento\Checkout\Model\Session')->unsetAll();
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
            /** @var $customerSession \Magento\Customer\Model\Session */
            $customerSession = \Mage::getSingleton('Magento\Customer\Model\Session');
            if (!$customerSession->isLoggedIn()) {
                $customerSession->setCustomerId(null)
                    ->setCustomerGroupId(null);
            }

            \Mage::getSingleton('Magento\Persistent\Model\Observer')->setQuoteGuest();
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
        \Mage::getSingleton('Magento\Core\Model\Session')->addNotice(
            __('Your shopping cart has been updated with new prices.')
        );
        $this->_redirect('checkout/cart');
    }
}
