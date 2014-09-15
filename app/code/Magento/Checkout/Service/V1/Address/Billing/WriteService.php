<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address\Billing;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Logger;
use \Magento\Checkout\Service\V1\QuoteLoader;
use \Magento\Sales\Model\Quote\AddressFactory;
use Magento\Framework\StoreManagerInterface;
use \Magento\Checkout\Service\V1\Address\Converter;
use \Magento\Checkout\Service\V1\Address\Validator;

class WriteService implements WriteServiceInterface
{
    /**
     * @var Validator
     */
    protected $addressValidator;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var AddressFactory
     */
    protected $quoteAddressFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Converter
     */
    protected $addressConverter;

    /**
     * @var QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @param QuoteLoader $quoteLoader
     * @param StoreManagerInterface $storeManager
     * @param Converter $addressConverter
     * @param Validator $addressValidator
     * @param AddressFactory $quoteAddressFactory
     * @param Logger $logger
     */
    public function __construct(
        QuoteLoader $quoteLoader,
        StoreManagerInterface $storeManager,
        Converter $addressConverter,
        Validator $addressValidator,
        AddressFactory $quoteAddressFactory,
        Logger $logger
    ) {
        $this->addressValidator = $addressValidator;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->quoteLoader = $quoteLoader;
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->addressConverter = $addressConverter;
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
        $quote->setBillingAddress($address);
        $quote->setDataChanges(true);
        try {
            $quote->save();
        } catch (\Exception $e) {
            $this->logger->logException($e);
            throw new InputException('Unable to save address. Please, check input data.');
        }
        return $quote->getBillingAddress()->getId();
    }
}
