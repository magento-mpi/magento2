<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;

class SetQuotePersistentData
{
    /**
     * Persistent session
     *
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSession = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Persistent data
     *
     * @var \Magento\PersistentHistory\Helper\Data
     */
    protected $_ePersistentData = null;

    /**
     * Persistent data
     *
     * @var \Magento\Persistent\Helper\Data
     */
    protected $_mPersistentData = null;

    /**
     * Whether set quote to be persistent in workflow
     *
     * @var bool
     */
    protected $_setQuotePersistent = true;

    /**
     * @param \Magento\Persistent\Helper\Session $persistentSession
     * @param \Magento\PersistentHistory\Helper\Data $ePersistentData
     * @param \Magento\Persistent\Helper\Data $mPersistentData
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Persistent\Helper\Session $persistentSession,
        \Magento\PersistentHistory\Helper\Data $ePersistentData,
        \Magento\Persistent\Helper\Data $mPersistentData,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_persistentSession = $persistentSession;
        $this->_mPersistentData = $mPersistentData;
        $this->_ePersistentData = $ePersistentData;
        $this->_customerSession = $customerSession;
    }

    /**
     * Set persistent data into quote
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute($observer)
    {
        if (!$this->_mPersistentData->canProcess($observer) || !$this->_persistentSession->isPersistent()) {
            return;
        }

        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return;
        }

        /** @var $customerSession \Magento\Customer\Model\Session */
        $customerSession = $this->_customerSession;

        $helper = $this->_ePersistentData;
        if ($helper->isCustomerAndSegmentsPersist() && $this->_setQuotePersistent) {
            $customerId = $customerSession->getCustomerId();
            if ($customerId) {
                $quote->setCustomerId($customerId);
            }
            $customerGroupId = $customerSession->getCustomerGroupId();
            if ($customerGroupId) {
                $quote->setCustomerGroupId($customerGroupId);
            }
        }
    }

    /**
     * Prevent setting persistent data into quote
     *
     * @param EventObserver $observer
     * @return void
     *
     * @see Observer::setQuotePersistentData
     */
    public function preventSettingQuotePersistent($observer)
    {
        $this->_setQuotePersistent = false;
    }
}
