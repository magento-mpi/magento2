<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * Quote shipping method data
 *
 * @codeCoverageIgnore
 */
class ShippingMethodConverter
{
    /**
     * @var ShippingMethodBuilder
     */
    protected $builder;

    /**
     * @param ShippingMethodBuilder $builder
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodBuilder $builder,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->builder = $builder;
        $this->storeManager = $storeManager;
    }

    /**
     * Convert rate model to ShippingMethod data object
     * @param string $quoteCurrencyCode
     * @param \Magento\Sales\Model\Quote\Address\Rate $rateModel
     *
     * @return \Magento\Checkout\Service\V1\Data\Cart\ShippingMethod
     */
    public function modelToDataObject($rateModel, $quoteCurrencyCode)
    {
        /** @var \Magento\Directory\Model\Currency $currency */
        $currency = $this->storeManager->getStore()->getBaseCurrency();

        $errorMessage = $rateModel->getErrorMessage();
        $data = [
            ShippingMethod::CARRIER_CODE => $rateModel->getCarrier(),
            ShippingMethod::METHOD_CODE => $rateModel->getMethod(),
            ShippingMethod::CARRIER_TITLE => $rateModel->getCarrierTitle(),
            ShippingMethod::METHOD_TITLE => $rateModel->getMethodTitle(),
            ShippingMethod::SHIPPING_AMOUNT => $currency->convert($rateModel->getPrice(), $quoteCurrencyCode),
            ShippingMethod::BASE_SHIPPING_AMOUNT => $rateModel->getPrice(),
            ShippingMethod::AVAILABLE => empty($errorMessage),
        ];
        $this->builder->populateWithArray($data);
        return $this->builder->create();
    }
}
