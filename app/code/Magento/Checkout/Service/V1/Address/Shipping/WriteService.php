<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address\Shipping;

use Magento\Framework\Exception\InputException;
use \Magento\Framework\Exception\NoSuchEntityException;

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
        if ($quote->isVirtual()) {
            throw new NoSuchEntityException(
                'Cart contains virtual product(s) only. Shipping address is not applicable'
            );
        }
        /** @var \Magento\Sales\Model\Quote\Address $address */
        $address = $this->quoteAddressFactory->create();
        $this->addressValidator->validate($addressData);
        if ($addressData->getId()) {
            $address->load($addressData->getId());
        }
        $address = $this->addressConverter->convertDataObjectToModel($addressData, $address);
        $address->setSameAsBilling(0);
        $quote->setShippingAddress($address);
        $quote->setDataChanges(true);
        try {
            $quote->save();
        } catch (\Exception $e) {
            throw new InputException('Unable to save address. Please, check input data.');
        }
        return $quote->getShippingAddress()->getId();
    }
}
