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
     * @var \Magento\Tax\Api\TaxCalculationInterface
     */
    protected $taxCalculationService;

    /**
     * @param \Magento\Tax\Helper\Data $helper
     * @param \Magento\Tax\Api\TaxCalculationInterface $taxCalculationService
     */
    public function __construct(
        \Magento\Tax\Helper\Data $helper,
        \Magento\Tax\Api\TaxCalculationInterface $taxCalculationService
    ) {
        $this->taxHelper = $helper;
        $this->taxCalculationService = $taxCalculationService;
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

        $defaultValue = $this->taxCalculationService->getDefaultCalculatedRate(
            $productClassId,
            $result['customerId']
        );
        $result['defaultTax'] = $defaultValue + $result['defaultTax'];

        $currentTax = $this->taxCalculationService->getCalculatedRate(
            $productClassId,
            $result['customerId']
        );
        $result['currentTax'] = $currentTax + $result['currentTax'];

        $adjustment = $product->getPriceInfo()->getAdjustment(\Magento\Tax\Pricing\Adjustment::ADJUSTMENT_CODE);

        $result['includeTax'] = $adjustment->isIncludedInBasePrice();
        $result['showIncludeTax'] = $this->taxHelper->displayPriceIncludingTax();
        $result['showBothPrices'] = $this->taxHelper->displayBothPrices();
        return $result;
    }
}
