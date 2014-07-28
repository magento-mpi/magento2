<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address\Shipping;

use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Checkout\Service\V1\Address\Converter as AddressConverter;

class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var AddressConverter
     */
    protected $addressConverter;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     * @param AddressConverter $addressConverter
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        AddressConverter $addressConverter,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->addressConverter = $addressConverter;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress($cartId)
    {
        $storeId = $this->storeManager->getStore()->getId();

        /** @var  \Magento\Sales\Model\Quote\Address $address */
        $address = $this->quoteLoader->load($cartId, $storeId)->getShippingAddress();
        return $this->addressConverter->convert($address);
    }
}
