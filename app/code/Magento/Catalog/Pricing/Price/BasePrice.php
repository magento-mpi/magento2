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

use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\PriceInfo\Base;

/**
 * Class BasePrice
 */
class BasePrice extends Price
{
    /**
     * Price type identifier string
     */
    const PRICE_TYPE_BASE_PRICE = 'base_price';

    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_BASE_PRICE;

    /**
     * @var bool|float
     */
    protected $value;

    /**
     * @var bool|float
     */
    protected $maxValue;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     */
    public function __construct(
        SaleableInterface $salableItem,
        $quantity = Base::PRODUCT_QUANTITY_DEFAULT
    ) {
        $this->salableItem = $salableItem;
        parent::__construct($salableItem, $quantity);
    }

    /**
     * Get Base Price Value
     *
     * @return float
     */
    public function getValue()
    {
        if (is_null($this->value)) {
            $this->value = false;
            foreach ($this->getPriceTypes() as $priceCode) {
                $price = $this->getPriceInfo()->getPrice($priceCode);
                if ($price instanceof OriginPrice && $price->getValue() !== false) {
                    if (is_null($this->value)) {
                        $this->value = $price->getValue();
                    } else {
                        $this->value = min($price->getValue(), $this->value);
                    }
                }
            }
        }
        return $this->value;
    }

    /**
     * Get array of price types
     *
     * @return array
     */
    protected function getPriceTypes()
    {
        $priceComposite = $this->getPriceInfo()->getPriceComposite();
        return array_diff(
            $priceComposite->getPriceCodes(),
            [self::PRICE_TYPE_BASE_PRICE, FinalPrice::PRICE_TYPE_FINAL, MsrpPrice::PRICE_TYPE_MSRP]
        );
    }

    /**
     * Get PriceInfo Object
     *
     * @return \Magento\Pricing\PriceInfoInterface
     */
    protected function getPriceInfo()
    {
        return $this->salableItem->getPriceInfo();
    }

    /**
     * Get Max Value
     *
     * @return bool|float
     */
    public function getMaxValue()
    {
        if (is_null($this->maxValue)) {
            $this->maxValue = false;
            foreach ($this->getPriceTypes() as $priceCode) {
                $price = $this->getPriceInfo()->getPrice($priceCode);
                if ($price instanceof OriginPrice && $price->getValue() !== false) {
                    if (is_null($this->maxValue)) {
                        $this->maxValue = $price->getValue();
                    } else {
                        $this->maxValue = max($price->getValue(), $this->maxValue);
                    }
                }
            }
        }
        return $this->maxValue;
    }
}
