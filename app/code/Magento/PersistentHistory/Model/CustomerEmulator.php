<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Model;

class CustomerEmulator
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

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
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Wishlist data
     *
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * Persistent data
     *
     * @var \Magento\PersistentHistory\Helper\Data
     */
    protected $_ePersistentData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Persistent\Helper\Session $persistentSession
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\PersistentHistory\Helper\Data $ePersistentData
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     */
    public function __construct(
        \Magento\Persistent\Helper\Session $persistentSession,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\PersistentHistory\Helper\Data $ePersistentData,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
    ) {
        $this->_persistentSession = $persistentSession;
        $this->_wishlistData = $wishlistData;
        $this->_ePersistentData = $ePersistentData;
        $this->_coreRegistry = $coreRegistry;
        $this->_customerFactory = $customerFactory;
        $this->_customerSession = $customerSession;
        $this->_customerAccountService = $customerAccountService;
    }

    /**
     * Emulate cutomer
     *
     * @return void
     */
    public function emulate()
    {
        /** TODO DataObject should be initialized instead of CustomerModel after refactoring of segment_customer */
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->_customerFactory->create()->load(
            $this->_persistentSession->getSession()->getCustomerId()
        );
        $this->_customerSession->setCustomerId($customer->getId())
            ->setCustomerGroupId($customer->getGroupId())
            ->setIsCustomerEmulated(true);

        // apply persistent data to segments
        $this->_coreRegistry->register('segment_customer', $customer, true);
        if ($this->_ePersistentData->isWishlistPersist()) {
            /** @var \Magento\Customer\Api\Data\CustomerInterface $customerDataObject */
            $customerDataObject = $this->_customerAccountService->getCustomer(
                $this->_persistentSession->getSession()->getCustomerId()
            );
            $this->_wishlistData->setCustomer($customerDataObject);
        }
    }
}
