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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Quote\AddressFactory $addressFactory
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
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
        $address = $quote->getShippingAddress();
        if (!$address->getId()) {
            throw new StateException('Shipping address is not set');
        }

        $address->setShippingMethod($carrierCode . '_' . $methodCode);
        if (!$address->requestShippingRates()) {
            throw new NoSuchEntityException('Carrier with such method not found: ' . $carrierCode . ', ' . $methodCode);
        }
        try {
            $address->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Cannot set shipping method. ' . $e->getMessage());
        }
        return true;
    }
}
