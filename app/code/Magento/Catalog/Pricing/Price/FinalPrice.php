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

/**
 * Final price model
 */
class FinalPrice extends AbstractPrice implements FinalPriceInterface
{
    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_FINAL;

    /**
     * @var
     */
    protected $basePrice;

    /**
     * @param \Magento\Pricing\Object\SaleableInterface $salableItem
     * @param float $quantity
     */
    public function __construct(
        \Magento\Pricing\Object\SaleableInterface $salableItem,
        $quantity = AbstractPrice::PRODUCT_QUANTITY_DEFAULT
    ) {
        $this->salableItem = $salableItem;
        $this->quantity = $quantity;
        $this->priceInfo = $salableItem->getPriceInfo();
        $this->basePrice = $this->priceInfo->getPrice('base_price');
        $this->baseAmount = $this->getValue();
    }

    /**
     * @return float
     */
    public function getMaxValue()
    {
        return $this->basePrice->getDisplayValue($this->basePrice->getMaxValue());
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->basePrice->getDisplayValue();
    }

    /**
     * @param float $baseAmount
     * @param string|null $excludedCode
     * @return float
     */
    public function getDisplayValue($baseAmount = null, $excludedCode = null)
    {
        return $this->basePrice->getDisplayValue($baseAmount, $excludedCode);
    }

    /**
     * @return float
     */
    public function getMinimalPrice()
    {
        return $this->getValue();
    }

    /**
     * @return float
     */
    public function getMaximumPrice()
    {
        return $this->getMaxValue();
    }
}
