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

use Magento\Pricing\Adjustment\CalculatorInterface;
use Magento\Pricing\Object\SaleableInterface;

/**
 * Final price model
 */
class FinalPrice extends AbstractPrice
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
     * @param SaleableInterface $product
     * @param float $quantity
     * @param CalculatorInterface $calculator
     */
    public function __construct(
        SaleableInterface $product,
        $quantity,
        CalculatorInterface $calculator
    ) {
        parent::__construct($product, $quantity, $calculator);
        $this->basePrice = $this->priceInfo->getPrice(BasePrice::PRICE_CODE);
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
     * @return \Magento\Pricing\Amount\AmountInterface
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
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMaximalPrice()
    {
        return $this->calculator->getAmount($this->getValue(), $this->salableItem);
    }
}
