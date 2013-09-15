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
namespace Magento\Persistent\Model\Observer;

class Session
{
    /**
     * Create/Update and Load session when customer log in
     *
     * @param \Magento\Event\Observer $observer
     */
    /**
     * Persistent session
     *
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSession = null;

    /**
     * Persistent data
     *
     * @var \Magento\Persistent\Helper\Data
     */
    protected $_persistentData = null;

    /**
     * @param \Magento\Persistent\Helper\Data $persistentData
     * @param \Magento\Persistent\Helper\Session $persistentSession
     */
    public function __construct(
        \Magento\Persistent\Helper\Data $persistentData,
        \Magento\Persistent\Helper\Session $persistentSession
    ) {
        $this->_persistentData = $persistentData;
        $this->_persistentSession = $persistentSession;
    }

    public function synchronizePersistentOnLogin(\Magento\Event\Observer $observer)
    {
        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $observer->getEvent()->getCustomer();
        // Check if customer is valid (remove persistent cookie for invalid customer)
        if (!$customer
            || !$customer->getId()
            || !$this->_persistentSession->isRememberMeChecked()
        ) {
            \Mage::getModel('Magento\Persistent\Model\Session')->removePersistentCookie();
            return;
        }

        $persistentLifeTime = $this->_persistentData->getLifeTime();
        // Delete persistent session, if persistent could not be applied
        if ($this->_persistentData->isEnabled() && ($persistentLifeTime <= 0)) {
            // Remove current customer persistent session
            \Mage::getModel('Magento\Persistent\Model\Session')->deleteByCustomerId($customer->getId());
            return;
        }

        /** @var $sessionModel \Magento\Persistent\Model\Session */
        $sessionModel = $this->_persistentSession->getSession();

        // Check if session is wrong or not exists, so create new session
        if (!$sessionModel->getId() || ($sessionModel->getCustomerId() != $customer->getId())) {
            $sessionModel = \Mage::getModel('Magento\Persistent\Model\Session')
                ->setLoadExpired()
                ->loadByCustomerId($customer->getId());
            if (!$sessionModel->getId()) {
                $sessionModel = \Mage::getModel('Magento\Persistent\Model\Session')
                    ->setCustomerId($customer->getId())
                    ->save();
            }

            $this->_persistentSession->setSession($sessionModel);
        }

        // Set new cookie
        if ($sessionModel->getId()) {
            \Mage::getSingleton('Magento\Core\Model\Cookie')->set(
                \Magento\Persistent\Model\Session::COOKIE_NAME,
                $sessionModel->getKey(),
                $persistentLifeTime
            );
        }
    }

    /**
     * Unload persistent session (if set in config)
     *
     * @param \Magento\Event\Observer $observer
     */
    public function synchronizePersistentOnLogout(\Magento\Event\Observer $observer)
    {
        if (!$this->_persistentData->isEnabled() || !$this->_persistentData->getClearOnLogout()) {
            return;
        }

        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $observer->getEvent()->getCustomer();
        // Check if customer is valid
        if (!$customer || !$customer->getId()) {
            return;
        }

        \Mage::getModel('Magento\Persistent\Model\Session')->removePersistentCookie();

        // Unset persistent session
        $this->_persistentSession->setSession(null);
    }

    /**
     * Synchronize persistent session info
     *
     * @param \Magento\Event\Observer $observer
     */
    public function synchronizePersistentInfo(\Magento\Event\Observer $observer)
    {
        if (!$this->_persistentData->isEnabled()
            || !$this->_persistentSession->isPersistent()
        ) {
            return;
        }

        /** @var $sessionModel \Magento\Persistent\Model\Session */
        $sessionModel = $this->_persistentSession->getSession();

        /** @var $request \Magento\Core\Controller\Request\Http */
        $request = $observer->getEvent()->getFront()->getRequest();

        // Quote Id could be changed only by logged in customer
        if (\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()
            || ($request && $request->getActionName() == 'logout' && $request->getControllerName() == 'account')
        ) {
            $sessionModel->save();
        }
    }

    /**
     * Set Checked status of "Remember Me"
     *
     * @param \Magento\Event\Observer $observer
     */
    public function setRememberMeCheckedStatus(\Magento\Event\Observer $observer)
    {
        if (!$this->_persistentData->canProcess($observer)
            || !$this->_persistentData->isEnabled()
            || !$this->_persistentData->isRememberMeEnabled()
        ) {
            return;
        }

        /** @var $controllerAction \Magento\Core\Controller\Varien\Action */
        $controllerAction = $observer->getEvent()->getControllerAction();
        if ($controllerAction) {
            $rememberMeCheckbox = $controllerAction->getRequest()->getPost('persistent_remember_me');
            $this->_persistentSession->setRememberMeChecked((bool)$rememberMeCheckbox);
            if (
                $controllerAction->getFullActionName() == 'checkout_onepage_saveBilling'
                    || $controllerAction->getFullActionName() == 'customer_account_createpost'
            ) {
                \Mage::getSingleton('Magento\Checkout\Model\Session')->setRememberMeChecked((bool)$rememberMeCheckbox);
            }
        }
    }

    /**
     * Renew persistent cookie
     *
     * @param \Magento\Event\Observer $observer
     */
    public function renewCookie(\Magento\Event\Observer $observer)
    {
        if (!$this->_persistentData->canProcess($observer)
            || !$this->_persistentData->isEnabled()
            || !$this->_persistentSession->isPersistent()
        ) {
            return;
        }

        /** @var $controllerAction \Magento\Core\Controller\Front\Action */
        $controllerAction = $observer->getEvent()->getControllerAction();

        if (\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()
            || $controllerAction->getFullActionName() == 'customer_account_logout'
        ) {
            \Mage::getSingleton('Magento\Core\Model\Cookie')->renew(
                \Magento\Persistent\Model\Session::COOKIE_NAME,
                $this->_persistentData->getLifeTime()
            );
        }
    }
}
