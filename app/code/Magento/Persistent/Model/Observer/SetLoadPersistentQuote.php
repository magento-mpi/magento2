<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Model\Observer;

class SetLoadPersistentQuote
{
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
     * @param \Magento\Persistent\Helper\Session $persistentSession
     * @param \Magento\Persistent\Helper\Data $quoteManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Persistent\Model\Persistent\ConfigFactory $eventManager
     */
    public function __construct(
        \Magento\Persistent\Helper\Session $persistentSession,
        \Magento\Persistent\Helper\Data $persistentData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_persistentSession = $persistentSession;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_persistentData = $persistentData;
    }


    /**
     * Set quote to be loaded even if not active
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute($observer)
    {
        if (!(
            ($this->_persistentSession->isPersistent() && !$this->_customerSession->isLoggedIn())
            && !$this->_persistentData->isShoppingCartPersist()
        )) {
            return;
        }

        if ($this->_checkoutSession) {
            $this->_checkoutSession->setLoadInactive();
        }
    }
}
