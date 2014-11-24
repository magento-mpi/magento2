<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Cart;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Authorization\Model\UserContextInterface;

/** 
 * Cart write service object. 
 */
class WriteService implements WriteServiceInterface
{
    /**
     * Quote factory.
     *
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * Quote repository.
     *
     * @var \Magento\Sales\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * Store manager interface.
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Customer registry.
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * User context interface.
     *
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * Quote factory.
     *
     * @var \Magento\Sales\Model\Service\QuoteFactory
     */
    protected $quoteServiceFactory;

    /**
     * Constructs a cart write service object.
     *
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory Quote factory.
     * @param \Magento\Sales\Model\QuoteRepository $quoteRepository Quote repository.
     * @param \Magento\Framework\StoreManagerInterface $storeManager Store manager.
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository Customer registry.
     * @param UserContextInterface $userContext User context.
     * @param \Magento\Sales\Model\Service\QuoteFactory $quoteServiceFactory Quote service factory.
     */
    public function __construct(
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\QuoteRepository $quoteRepository,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        UserContextInterface $userContext,
        \Magento\Sales\Model\Service\QuoteFactory $quoteServiceFactory
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->quoteRepository = $quoteRepository;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->userContext = $userContext;
        $this->quoteServiceFactory = $quoteServiceFactory;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException The empty cart and quote could not be created.
     * @return int Cart ID.
     */
    public function create()
    {
        $quote = $this->userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER
            ? $this->createCustomerCart()
            : $this->createAnonymousCart();

        try {
            $quote->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Cannot create quote');
        }
        return $quote->getId();
    }

    /**
     * Creates an anonymous cart.
     *
     * @return \Magento\Sales\Model\Quote Cart object.
     */
    protected function createAnonymousCart()
    {
        $storeId = $this->storeManager->getStore()->getId();
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteFactory->create();
        $quote->setStoreId($storeId);
        return $quote;
    }

    /**
     * Creates a cart for the currently logged-in customer.
     *
     * @return \Magento\Sales\Model\Quote Cart object.
     * @throws CouldNotSaveException The cart could not be created.
     */
    protected function createCustomerCart()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $customer = $this->customerRepository->getById($this->userContext->getUserId());

        $currentCustomerQuote = $this->quoteFactory->create()->loadByCustomer($customer);
        if ($currentCustomerQuote->getId() && $currentCustomerQuote->getIsActive()) {
            throw new CouldNotSaveException('Cannot create quote');
        }

        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteFactory->create();
        $quote->setStoreId($storeId);
        $quote->setCustomer($customer);
        $quote->setCustomerIsGuest(0);
        return $quote;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $cartId The cart ID.
     * @param int $customerId The customer ID.
     * @return boolean
     * @throws \Magento\Framework\Exception\StateException The customer cannot be assigned to the specified cart: The cart belongs to a different store or is not anonymous, or the customer already has an active cart.
     */
    public function assignCustomer($cartId, $customerId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $quote = $this->quoteRepository->get($cartId);
        $customer = $this->customerRepository->getById($customerId);
        if (!in_array($storeId, $quote->getSharedStoreIds())) {
            throw new StateException('Cannot assign customer to the given cart. The cart belongs to different store.');
        }
        if ($quote->getCustomerId()) {
            throw new StateException('Cannot assign customer to the given cart. The cart is not anonymous.');
        }
        $currentCustomerQuote = $this->quoteFactory->create()->loadByCustomer($customer);
        if ($currentCustomerQuote->getId()) {
            throw new StateException('Cannot assign customer to the given cart. Customer already has active cart.');
        }

        $quote->setCustomer($customer);
        $quote->setCustomerIsGuest(0);
        $quote->save();
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $cartId The cart ID.
     * @return int Order ID.
     */
    public function order($cartId)
    {
        $quote = $this->quoteRepository->get($cartId);
        /** @var \Magento\Sales\Model\Service\Quote $quoteService */
        $quoteService = $this->quoteServiceFactory->create(['quote' => $quote]);
        $order = $quoteService->submitOrderWithDataObject();
        return $order->getId();
    }
}
