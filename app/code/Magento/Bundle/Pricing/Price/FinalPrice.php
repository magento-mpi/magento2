<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\Pricing\Object\SaleableInterface;
use Magento\Bundle\Pricing\Adjustment\BundleCalculatorInterface;

/**
 * Final price model
 */
class FinalPrice extends \Magento\Catalog\Pricing\Price\FinalPrice
{
    /**
     * Price type final
     */
    const PRICE_CODE = 'final_price';

    /**
     * @var BundleCalculatorInterface
     */
    protected $calculator;

    /**
     * @param SaleableInterface $product
     * @param float $quantity
     * @param BundleCalculatorInterface $calculator
     */
    public function __construct(
        SaleableInterface $product,
        $quantity,
        BundleCalculatorInterface $calculator
    ) {
        parent::__construct($product, $quantity, $calculator);
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return parent::getValue() + $this->basePrice->applyDiscount($this->getBundleOptionPrice()->getValue());
    }

    /**
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMaximalPrice()
    {
        return $this->calculator->getMaxAmount($this->basePrice->getValue(), $this->salableItem);
    }

    /**
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMinimalPrice()
    {
        return $this->getAmount();
    }


    /**
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getAmount()
    {
        return $this->calculator->getAmount(parent::getValue(), $this->salableItem);
    }

    /**
     * @return \Magento\Bundle\Pricing\Price\BundleOptionPrice
     */
    protected function getBundleOptionPrice()
    {
        return $this->priceInfo->getPrice(BundleOptionPrice::PRICE_CODE, $this->quantity);
    }
}
