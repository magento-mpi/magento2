<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address\Shipping;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Logger;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var \Magento\Framework\StoreManagerInterface
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
     * @var Logger
     */
    protected $logger;

    /**
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Service\V1\Address\Converter $addressConverter
     * @param \Magento\Checkout\Service\V1\Address\Validator $addressValidator
     * @param \Magento\Sales\Model\Quote\AddressFactory $quoteAddressFactory
     * @param Logger $logger
     */
    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Checkout\Service\V1\Address\Converter $addressConverter,
        \Magento\Checkout\Service\V1\Address\Validator $addressValidator,
        \Magento\Sales\Model\Quote\AddressFactory $quoteAddressFactory,
        Logger $logger
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->addressConverter = $addressConverter;
        $this->addressValidator = $addressValidator;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
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
            $this->logger->logException($e);
            throw new InputException('Unable to save address. Please, check input data.');
        }
        return $quote->getShippingAddress()->getId();
    }
}
