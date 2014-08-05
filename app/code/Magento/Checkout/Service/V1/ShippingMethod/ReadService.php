<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\ShippingMethod;

use \Magento\Framework\Exception\NoSuchEntityException;
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
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ShippingMethodBuilder $methodBuilder
     */
    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodBuilder $methodBuilder
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->storeManager = $storeManager;
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
        if ($quote->isVirtual()) {
            throw new NoSuchEntityException(
                'Cart contains virtual product(s) only. Shipping method is not applicable'
            );
        }
        $shippingAddress = $quote->getShippingAddress();

        $shippingMethod = $shippingAddress->getShippingMethod();
        if ($shippingMethod) {
            $shippingMethodData = explode("_", $shippingMethod);
            $output[ShippingMethod::KEY_CARRIER_CODE] = array_shift($shippingMethodData);
            $output[ShippingMethod::KEY_METHOD_CODE] = array_shift($shippingMethodData);
        } else {
            throw new NoSuchEntityException('Shipping method and carrier are not set for the quote');
        }

        $shippingDescription = $shippingAddress->getShippingDescription();
        $output[ShippingMethod::KEY_DESCRIPTION] = ($shippingDescription) ? $shippingDescription : '';
        $output[ShippingMethod::KEY_SHIPPING_AMOUNT] = (float) $shippingAddress->getShippingAmount();

        return $this->methodBuilder->populateWithArray($output)->create();
    }
}
