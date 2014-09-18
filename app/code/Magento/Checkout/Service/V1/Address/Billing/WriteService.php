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
use \Magento\Sales\Model\QuoteRepository;
use \Magento\Sales\Model\Quote\AddressFactory;
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
     * @var Converter
     */
    protected $addressConverter;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @param QuoteRepository $quoteRepository
     * @param Converter $addressConverter
     * @param Validator $addressValidator
     * @param AddressFactory $quoteAddressFactory
     * @param Logger $logger
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        Converter $addressConverter,
        Validator $addressValidator,
        AddressFactory $quoteAddressFactory,
        Logger $logger
    ) {
        $this->addressValidator = $addressValidator;
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->addressConverter = $addressConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress($cartId, $addressData)
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
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
