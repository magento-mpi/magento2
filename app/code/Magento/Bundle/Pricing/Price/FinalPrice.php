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

/**
 * Final price model
 */
class FinalPrice extends \Magento\Catalog\Pricing\Price\FinalPrice
{
    /**
     * @var Calculator
     */
    protected $calculator;

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
     * @return float
     */
    public function getMinimalPrice()
    {
        return $this->calculator->getAmount(parent::getValue(), $this->salableItem);
    }

    /**
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getMaximalPrice()
    {
        return $this->calculator->getMaxAmount(parent::getMaxValue(), $this->salableItem);
    }

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
