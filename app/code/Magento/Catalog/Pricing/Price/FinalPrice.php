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

use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\Object\SaleableInterface;

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
     * @param CalculatorInterface $calculator
     */
    public function __construct(
        SaleableInterface $salableItem,
        $quantity,
        CalculatorInterface $calculator
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
     * Get Minimal Price Amount
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMinimalPrice()
    {
        $minimalPrice = $this->salableItem->getMinimalPrice();
        if ($minimalPrice === null) {
            $minimalPrice = $this->getValue();
        }
        return $this->calculator->getAmount($minimalPrice, $this->salableItem);
    }

    /**
     * Get Maximal Price Amount
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMaximalPrice()
    {
        return $this->calculator->getAmount($this->getValue(), $this->salableItem);
    }
}
