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

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Sales\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     * @param \Magento\Sales\Model\QuoteRepository $quoteRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param UserContextInterface $userContext
     */
    public function __construct(
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\QuoteRepository $quoteRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        UserContextInterface $userContext

    ) {
        $this->quoteFactory = $quoteFactory;
        $this->quoteRepository = $quoteRepository;
        $this->storeManager = $storeManager;
        $this->customerRegistry = $customerRegistry;
        $this->userContext = $userContext;
    }

    /**
     * {@inheritdoc}
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
     * Create anonymous cart
     *
     * @return \Magento\Sales\Model\Quote
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
     * Create cart for current logged in customer
     *
     * @return \Magento\Sales\Model\Quote
     * @throws CouldNotSaveException
     */
    protected function createCustomerCart()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $customer = $this->customerRegistry->retrieve($this->userContext->getUserId());

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
     * {@inheritdoc}
     */
    public function assignCustomer($cartId, $customerId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $quote = $this->quoteRepository->get($cartId);
        $customer = $this->customerRegistry->retrieve($customerId);
        if (!in_array($storeId, $customer->getSharedStoreIds())) {
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
}
