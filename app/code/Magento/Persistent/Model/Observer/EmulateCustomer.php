<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Model\Observer;

class EmulateCustomer
{
    /**
     * Customer account service
     *
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

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
     * @param \Magento\Persistent\Helper\Data $persistentData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     */
    public function __construct(
        \Magento\Persistent\Helper\Session $persistentSession,
        \Magento\Persistent\Helper\Data $persistentData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
    ) {
        $this->_persistentSession = $persistentSession;
        $this->_persistentData = $persistentData;
        $this->_customerSession = $customerSession;
        $this->_customerAccountService = $customerAccountService;
    }

    /**
     * Set persistent data to customer session
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute($observer)
    {
        if (!$this->_persistentData->canProcess($observer) || !$this->_persistentData->isShoppingCartPersist()) {
            return $this;
        }

        if ($this->_persistentSession->isPersistent() && !$this->_customerSession->isLoggedIn()) {
            $customer = $this->_customerAccountService->getCustomer(
                $this->_persistentSession->getSession()->getCustomerId()
            );
            $this->_customerSession->setCustomerId($customer->getId())->setCustomerGroupId($customer->getGroupId());
        }
        return $this;
    }
}
