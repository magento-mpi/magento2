<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\ShippingMethod;

use \Magento\Framework\Exception\CouldNotSaveException;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Exception\InputException;
use \Magento\Framework\Exception\StateException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Sales\Model\Quote\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Quote\AddressFactory $addressFactory
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     */
    public function __construct(
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Quote\AddressFactory $addressFactory,
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
    ) {
        $this->addressFactory = $addressFactory;
        $this->quoteLoader = $quoteLoader;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod($cartId, $carrierCode, $methodCode)
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $this->storeManager->getStore()->getId());
        if (0 == $quote->getItemsCount()) {
            throw new InputException('Shipping method is not applicable for empty cart');
        }

        if ($quote->isVirtual()) {
            throw new NoSuchEntityException(
                'Cart contains virtual product(s) only. Shipping method is not applicable.'
            );
        }
        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress->getCountryId()) {
            throw new StateException('Shipping address is not set');
        }
        $billingAddress = $quote->getBillingAddress();
        if (!$billingAddress->getCountryId()) {
            throw new StateException('Billing address is not set');
        }

        $shippingAddress->setShippingMethod($carrierCode . '_' . $methodCode);
        if (!$shippingAddress->requestShippingRates()) {
            throw new NoSuchEntityException('Carrier with such method not found: ' . $carrierCode . ', ' . $methodCode);
        }
        try {
            $quote->collectTotals()->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Cannot set shipping method. ' . $e->getMessage());
        }
        return true;
    }
}
