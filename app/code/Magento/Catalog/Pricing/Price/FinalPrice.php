<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Price\AbstractPrice;

/**
 * Final price model
 */
class FinalPrice extends AbstractPrice implements FinalPriceInterface
{
    /**
     * Price type final
     */
    const PRICE_CODE = 'final_price';

    /**
     * @var BasePrice
     */
    protected $basePrice;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator
    ) {
        parent::__construct($saleableItem, $quantity, $calculator);
        $this->basePrice = $this->priceInfo->getPrice(BasePrice::PRICE_CODE);
    }

    /**
     * Get Value
     *
     * @return float|bool
     */
    public function getValue()
    {
        return max(0, $this->basePrice->getValue());
    }

    /**
     * Get Minimal Price Amount
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMinimalPrice()
    {
        $minimalPrice = $this->product->getMinimalPrice();
        if ($minimalPrice === null) {
            $minimalPrice = $this->getValue();
        }
        return $this->calculator->getAmount($minimalPrice, $this->product);
    }

    /**
     * Get Maximal Price Amount
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMaximalPrice()
    {
        return $this->calculator->getAmount($this->getValue(), $this->product);
    }
}
