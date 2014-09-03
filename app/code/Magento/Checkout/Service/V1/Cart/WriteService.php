<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Cart;

use Magento\Framework\Exception\CouldNotSaveException;
use \Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

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
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     * @param \Magento\Sales\Model\QuoteRepository $quoteRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     */
    public function __construct(
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\QuoteRepository $quoteRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->quoteRepository = $quoteRepository;
        $this->storeManager = $storeManager;
        $this->customerRegistry = $customerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $storeId = $this->storeManager->getStore()->getId();

        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteFactory->create();
        $quote->setStoreId($storeId);
        try {
            $quote->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Cannot create quote');
        }
        return $quote->getId();
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
