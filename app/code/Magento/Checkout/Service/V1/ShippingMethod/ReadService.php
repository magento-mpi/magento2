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
        $output = [];

        $storeId = $this->storeManager->getStore()->getId();

        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $storeId);

        /** @var \Magento\Sales\Model\Quote\Address $shippingAddress */
        $shippingAddress = $quote->getShippingAddress();

        $shippingMethod = $shippingAddress->getShippingMethod();
        if ($shippingMethod) {
            $shippingMethodData = explode("_", $shippingMethod);
            $output[ShippingMethod::CARRIER_CODE] = array_shift($shippingMethodData);
            $output[ShippingMethod::METHOD_CODE] = array_shift($shippingMethodData);
        } else {
            return null;
        }

        $shippingDescription = $shippingAddress->getShippingDescription();
        $output[ShippingMethod::DESCRIPTION] = ($shippingDescription) ? $shippingDescription : '';
        $output[ShippingMethod::SHIPPING_AMOUNT] = (float) $shippingAddress->getShippingAmount();
        $output[ShippingMethod::BASE_SHIPPING_AMOUNT] = (float) $shippingAddress->getBaseShippingAmount();

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

        $quote->getShippingAddress()->requestShippingRates();
        $shippingRates = $quote->getShippingAddress()->getAllShippingRates();
        foreach ($shippingRates as $rate) {
            $output[] = $this->converter->modelToDataObject($rate);
        }
        return $output;
    }
}
