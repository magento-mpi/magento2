<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address\Shipping;

use \Magento\Checkout\Service\V1\Address\Converter as AddressConverter;
use \Magento\Framework\Exception\NoSuchEntityException;

class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Sales\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var AddressConverter
     */
    protected $addressConverter;

    /**
     * @param \Magento\Sales\Model\QuoteRepository $quoteRepository
     * @param AddressConverter $addressConverter
     */
    public function __construct(
        \Magento\Sales\Model\QuoteRepository $quoteRepository,
        AddressConverter $addressConverter
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->addressConverter = $addressConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress($cartId)
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        if ($quote->isVirtual()) {
            throw new NoSuchEntityException(
                'Cart contains virtual product(s) only. Shipping address is not applicable'
            );
        }

        /** @var \Magento\Sales\Model\Quote\Address $address */
        $address = $quote->getShippingAddress();
        return $this->addressConverter->convertModelToDataObject($address);
    }
}
