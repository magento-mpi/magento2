<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\ShippingMethod;

use \Magento\Sales\Model\QuoteRepository;
use \Magento\Checkout\Service\V1\Data\Cart\ShippingMethod;
use \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodConverter;
use \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodBuilder;
use \Magento\Framework\Exception\StateException;
use \Magento\Framework\Exception\InputException;

class ReadService implements ReadServiceInterface
{
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodBuilder
     */
    protected $methodBuilder;

    /**
     * @var ShippingMethodConverter
     */
    protected $converter;

    /**
     * @param QuoteRepository $quoteRepository
     * @param ShippingMethodConverter $converter
     * @param \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodBuilder $methodBuilder
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        ShippingMethodConverter $converter,
        \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodBuilder $methodBuilder
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->converter = $converter;
        $this->methodBuilder = $methodBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod($cartId)
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);

        /** @var \Magento\Sales\Model\Quote\Address $shippingAddress */
        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress->getCountryId()) {
            throw new StateException('Shipping address not set.');
        }

        $shippingMethod = $shippingAddress->getShippingMethod();
        if (!$shippingMethod) {
            return null;
        }

        list($carrierCode, $methodCode) = $this->divideNames('_', $shippingAddress->getShippingMethod());
        list($carrierTitle, $methodTitle) = $this->divideNames(' - ', $shippingAddress->getShippingDescription());

        $output = [
            ShippingMethod::CARRIER_CODE => $carrierCode,
            ShippingMethod::METHOD_CODE => $methodCode,
            ShippingMethod::CARRIER_TITLE => $carrierTitle,
            ShippingMethod::METHOD_TITLE => $methodTitle,
            ShippingMethod::SHIPPING_AMOUNT => $shippingAddress->getShippingAmount(),
            ShippingMethod::BASE_SHIPPING_AMOUNT => $shippingAddress->getBaseShippingAmount(),
            ShippingMethod::AVAILABLE => true,
        ];

        return $this->methodBuilder->populateWithArray($output)->create();
    }

    /**
     * @param string $delimiter
     * @param string $line
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function divideNames($delimiter, $line)
    {
        if (strpos($line, $delimiter) === false) {
            throw new InputException('Line "' .  $line . '" doesn\'t contain delimiter ' . $delimiter);
        }
        return explode($delimiter, $line);
    }

    /**
     * {@inheritdoc}
     */
    public function getList($cartId)
    {
        $output = [];

        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }

        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress->getCountryId()) {
            throw new StateException('Shipping address not set.');
        }
        $shippingAddress->collectShippingRates();
        $shippingRates = $shippingAddress->getGroupedAllShippingRates();
        foreach ($shippingRates as $carrierRates) {
            foreach ($carrierRates as $rate) {
                $output[] = $this->converter->modelToDataObject($rate, $quote->getQuoteCurrencyCode());
            }
        }
        return $output;
    }
}
