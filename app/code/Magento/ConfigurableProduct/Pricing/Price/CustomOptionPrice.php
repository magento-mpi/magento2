<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Pricing\Price;

use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Pricing\Amount\AmountInterface;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Adjustment\CalculatorInterface;
use Magento\Catalog\Model\Product\PriceModifierInterface;

/**
 * Class CustomOptionPrice
 *
 * @package Magento\ConfigurableProduct\Pricing\Price
 */
class CustomOptionPrice extends RegularPrice implements CustomOptionPriceInterface
{
    /**
     * @var PriceModifierInterface
     */
    protected $priceModifier;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param PriceModifierInterface $modifier
     */
    public function __construct(
        SaleableInterface $salableItem,
        $quantity,
        CalculatorInterface $calculator,
        PriceModifierInterface $modifier
    ) {
        $this->priceModifier = $modifier;
        parent::__construct($salableItem, $quantity, $calculator);
    }

    /**
     * Get Option Value
     *
     * @param array $value
     * @return AmountInterface
     */
    public function getOptionValueAmount(array $value = array())
    {
        $pricingValue = $this->getPricingValue($value);
        $this->salableItem->setParentId(true);
        $amount = $this->priceModifier->modifyPrice($pricingValue, $this->salableItem);

        return $this->calculator->getAmount($amount, $this->salableItem);

    }

    /**
     * Get Option Value Amount with no Catalog Rules
     *
     * @param array $value
     * @return AmountInterface
     */
    public function getOptionValueOldAmount(array $value = array())
    {
        $amount = $this->getPricingValue($value);

        return $this->calculator->getAmount($amount, $this->salableItem);
    }

    /**
     * Prepare percent price value
     *
     * @param $value
     * @return float
     */
    protected function preparePrice($value)
    {
        return $this->salableItem->getPriceInfo()->getPrice('final_price')->getValue()
        * $value['pricing_value'] / 100;
    }

    /**
     * Get value from array
     *
     * @param array $value
     * @return float
     */
    protected function getPricingValue(array $value = array())
    {
        if ($value['is_percent'] && !empty($value['pricing_value'])) {
            return $this->preparePrice($value);
        } else {
            return $value['pricing_value'];
        }
    }
}
