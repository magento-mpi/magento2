<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Pricing\Adjustment\Calculator;
use Magento\Pricing\Object\SaleableInterface;

/**
 * Final price model
 */
class FinalPrice extends RegularPrice implements FinalPriceInterface
{
    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_FINAL;

    /**
     * @var BasePrice
     */
    protected $basePrice;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     * @param Calculator $calculator
     */
    public function __construct(
        SaleableInterface $salableItem,
        $quantity,
        Calculator $calculator
    ) {
        parent::__construct($salableItem, $quantity, $calculator);
        $this->basePrice = $this->priceInfo->getPrice(BasePrice::PRICE_TYPE_BASE_PRICE);
        $this->baseAmount = $this->getValue();
    }

    /**
     * Get Value
     *
     * @return float|bool
     */
    public function getValue()
    {
        return max(0, $this->basePrice->getValue()); // + custom options price
    }

    /**
     * Get MaxValue
     *
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMaxValue()
    {
        return $this->basePrice->getMaxValue();
    }

    /**
     * Get Minimal Price
     *
     * @return float
     */
    public function getMinimalPrice()
    {
        return $this->calculator->getAmount($this->getValue(), $this->salableItem);
    }

    /**
     * Get Maximal Price
     *
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMaximalPrice()
    {
        return $this->calculator->getAmount($this->getMaxValue(), $this->salableItem);
    }
}
