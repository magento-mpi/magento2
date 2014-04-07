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
     * @var BundleCalculatorInterface
     */
    protected $calculator;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     * @param BundleCalculatorInterface $calculator
     */
    public function __construct(
        SaleableInterface $salableItem,
        $quantity,
        BundleCalculatorInterface $calculator
    ) {
        parent::__construct($salableItem, $quantity, $calculator);
    }

    /**
     * @return float|bool
     */
    public function getValue()
    {
        return parent::getValue() + $this->basePrice->applyDiscount($this->getBundleOptionPrice()->getValue());
    }

    /**
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMaxValue()
    {
        return $this->basePrice->getMaxValue() + $this->basePrice->applyDiscount(
            $this->getBundleOptionPrice()->getMaxValue()
        );
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
    public function getMaximalPrice()
    {
        return $this->calculator->getMaxAmount(parent::getMaxValue(), $this->salableItem);
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
        return $this->priceInfo->getPrice(BundleOptionPriceInterface::PRICE_TYPE_BUNDLE_OPTION, $this->quantity);
    }
}
