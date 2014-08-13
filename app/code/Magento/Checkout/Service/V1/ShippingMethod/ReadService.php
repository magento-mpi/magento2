<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\ShippingMethod;

use \Magento\Checkout\Service\V1\Data\Cart\ShippingMethod;
use \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodBuilder;
use \Magento\Framework\Exception\StateException;

class ReadService implements ReadServiceInterface
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
     * @var \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodBuilder
     */
    protected $methodBuilder;

    /**
     * @var \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodConverter
     */
    protected $converter;

    /**
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodConverter $converter
     * @param ShippingMethodBuilder $methodBuilder
     */
    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodConverter $converter,
        \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodBuilder $methodBuilder
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->storeManager = $storeManager;
        $this->converter = $converter;
        $this->methodBuilder = $methodBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod($cartId)
    {
        $storeId = $this->storeManager->getStore()->getId();

        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $storeId);

        /** @var \Magento\Sales\Model\Quote\Address $shippingAddress */
        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress->getId()) {
            throw new StateException('Shipping address not set.');
        }

        $shippingMethod = $shippingAddress->getShippingMethod();
        if (!$shippingMethod) {
            return null;
        }

        list($carrierCode, $methodCode) = explode('_', $shippingAddress->getShippingMethod());
        list($carrierTitle, $methodTitle) = explode(' - ', $shippingAddress->getShippingDescription());

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
     * {@inheritdoc}
     */
    public function getList($cartId)
    {
        $output = [];

        $storeId = $this->storeManager->getStore()->getId();

        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $storeId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }

        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress->getId()) {
            throw new StateException('Shipping address not set.');
        }
        $shippingAddress->requestShippingRates();
        $shippingRates = $quote->getShippingAddress()->getAllShippingRates();
        foreach ($shippingRates as $rate) {
            $output[] = $this->converter->modelToDataObject($rate, $quote->getQuoteCurrencyCode());
        }
        return $output;
    }
}
