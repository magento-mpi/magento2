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
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

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
     * @param \Magento\Sales\Model\QuoteRepository $quoteRepository Quote repository.
     * @param \Magento\Framework\StoreManagerInterface $storeManager Store manager.
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry Customer registry.
     * @param UserContextInterface $userContext User context.
     * @param \Magento\Sales\Model\Service\QuoteFactory $quoteServiceFactory Quote service factory.
     */
    public function __construct(
        \Magento\Sales\Model\QuoteRepository $quoteRepository,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        UserContextInterface $userContext,
        \Magento\Sales\Model\Service\QuoteFactory $quoteServiceFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->storeManager = $storeManager;
        $this->customerRegistry = $customerRegistry;
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
            $this->quoteRepository->save($quote);
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
        $quote = $this->quoteRepository->create();
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
        $customer = $this->customerRegistry->retrieve($this->userContext->getUserId());

        try {
            $this->quoteRepository->getActiveForCustomer($this->userContext->getUserId());
            throw new CouldNotSaveException('Cannot create quote');
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
        }

        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->create();
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
        $quote = $this->quoteRepository->getActive($cartId);
        $customer = $this->customerRegistry->retrieve($customerId);
        if (!in_array($storeId, $customer->getSharedStoreIds())) {
            throw new StateException('Cannot assign customer to the given cart. The cart belongs to different store.');
        }
        if ($quote->getCustomerId()) {
            throw new StateException('Cannot assign customer to the given cart. The cart is not anonymous.');
        }
        try {
            $this->quoteRepository->getForCustomer($customerId);
            throw new StateException('Cannot assign customer to the given cart. Customer already has active cart.');
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
        }

        $quote->setCustomer($customer);
        $quote->setCustomerIsGuest(0);
        $this->quoteRepository->save($quote);
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
        $quote = $this->quoteRepository->getActive($cartId);
        /** @var \Magento\Sales\Model\Service\Quote $quoteService */
        $quoteService = $this->quoteServiceFactory->create(['quote' => $quote]);
        $order = $quoteService->submitOrderWithDataObject();
        return $order->getId();
    }
}
