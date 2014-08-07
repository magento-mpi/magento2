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
     * @param \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodBuilder $builder
     */
    public function __construct(
        \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodBuilder $builder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->builder = $builder;
        $this->storeManager = $storeManager;
    }

    /**
     * Convert rate model to ShippingMethod data object
     *
     * @param \Magento\Sales\Model\Quote\Address\Rate $rateModel
     * @return \Magento\Checkout\Service\V1\Data\Cart\ShippingMethod
     */
    public function modelToDataObject($rateModel)
    {
        $currency = $this->storeManager->getStore()->getCurrentCurrency();

        $data = [
            ShippingMethod::CARRIER_CODE => $rateModel->getCarrier(),
            ShippingMethod::METHOD_CODE => $rateModel->getMethod(),
            ShippingMethod::BASE_SHIPPING_AMOUNT => $rateModel->getPrice(),
            ShippingMethod::SHIPPING_AMOUNT => $currency->convert($rateModel->getPrice(), $currency),
            ShippingMethod::DESCRIPTION => $rateModel->getCarrierTitle(),
        ];
        $this->builder->populateWithArray($data);
        return $this->builder->create();
    }
}
