<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Pricing\Price\Plugin;

/**
 * Class AttributePrice
 */
class AttributePrice
{
    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxHelper;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    protected $calculation;

    /**
     * @param \Magento\Tax\Helper\Data $helper
     * @param \Magento\Tax\Model\Calculation $calculation
     */
    public function __construct(
        \Magento\Tax\Helper\Data $helper,
        \Magento\Tax\Model\Calculation $calculation
    ) {
        $this->taxHelper = $helper;
        $this->calculation = $calculation;
    }

    /**
     * Get Tax Adjustments for configurable product
     *
     * @param \Magento\ConfigurableProduct\Pricing\Price\AttributePrice $attribute
     * @param array $result
     * @return array
     */
    public function afterPrepareAdjustmentConfig(
        \Magento\ConfigurableProduct\Pricing\Price\AttributePrice $attribute,
        array $result
    ) {
        $product = $result['product'];

        $productClassId = $product->getTaxClassId();

        $defaultValue = $this->applyRate($productClassId, false, false, false);
        $result['defaultTax'] = $defaultValue + $result['defaultTax'];

        $currentTax = $this->applyRate($productClassId);
        $result['currentTax'] = $currentTax + $result['currentTax'];

        $adjustment = $product->getPriceInfo()->getAdjustment(\Magento\Tax\Pricing\Adjustment::ADJUSTMENT_CODE);

        $result['includeTax'] = $adjustment->isIncludedInBasePrice();
        $result['showIncludeTax'] = $this->taxHelper->displayPriceIncludingTax();
        $result['showBothPrices'] = $this->taxHelper->displayBothPrices();
        return $result;
    }

    /**
     * Apply Tax Rate
     *
     * @param int $classId
     * @param null $shippingAddress
     * @param null $billingAddress
     * @param null $customerTaxClass
     * @return float
     */
    protected function applyRate($classId, $shippingAddress = null, $billingAddress = null, $customerTaxClass = null)
    {
        $rateRequest = $this->calculation->getRateRequest($shippingAddress, $billingAddress, $customerTaxClass);
        $rateRequest->setProductClassId($classId);
        return $this->calculation->getRate($rateRequest);
    }
}
