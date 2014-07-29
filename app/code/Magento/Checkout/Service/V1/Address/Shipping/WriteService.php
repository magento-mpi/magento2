<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address\Shipping;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Sales\Model\Quote\AddressFactory
     */
    protected $quoteAddressFactory;

    /**
     * @var \Magento\Checkout\Service\V1\Address\Converter
     */
    protected $addressConverter;

    /**
     * @var \Magento\Checkout\Service\V1\Address\Validator
     */
    protected $addressValidator;

    /**
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Service\V1\Address\Converter $addressConverter
     * @param \Magento\Checkout\Service\V1\Address\Validator $addressValidator
     * @param \Magento\Sales\Model\Quote\AddressFactory $quoteAddressFactory
     */
    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Service\V1\Address\Converter $addressConverter,
        \Magento\Checkout\Service\V1\Address\Validator $addressValidator,
        \Magento\Sales\Model\Quote\AddressFactory $quoteAddressFactory
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->addressConverter = $addressConverter;
        $this->addressValidator = $addressValidator;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress($cartId, $addressData)
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $this->storeManager->getStore()->getId());
        /** @var \Magento\Sales\Model\Quote\Address $address */
        $address = $this->quoteAddressFactory->create();
        $this->addressValidator->validate($addressData);
        if ($addressData->getId()) {
            $address->load($addressData->getId());
        }
        $address = $this->addressConverter->convertDataObjectToModel($addressData, $address);
        $quote->setShippingAddress($address);
        $quote->setDataChanges(true);
        $quote->save();
        return true;
    }
}
