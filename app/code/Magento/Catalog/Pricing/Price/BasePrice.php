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
    protected $value = false;

    /**
     * @var bool|float
     */
    protected $maxValue = false;

    public function __construct(SaleableInterface $salableItem, $quantity = Base::PRODUCT_QUANTITY_DEFAULT)
    {
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
        foreach ($this->getPriceTypes() as $priceCode) {
            $price = $this->getPriceInfo()->getPrice($priceCode);
            if ($price instanceof OriginPrice && false !== $price->getValue()) {
                if (null === $this->baseAmount) {
                    $this->baseAmount = $price->getValue();
                } else {
                    $this->baseAmount = min($price->getValue(), $this->baseAmount);
                }
            }
        }
        return $this->baseAmount;
    }

    /**
     * Get array of price types
     *
     * @return array
     */
    protected function getPriceTypes()
    {
        $priceComposite = $this->getPriceInfo()->getPriceComposite();
        return array_diff($priceComposite->getPriceCodes(), [$this->priceType]);
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
        if (!$this->maxValue) {
            $priceComposite = $this->salableItem->getPriceInfo()->getPriceComposite();
            foreach (array_diff($priceComposite->getPriceCodes(), array($this->priceType)) as $priceCode) {
                $price = $this->salableItem->getPriceInfo()->getPrice($priceCode);
                if ($price instanceof OriginPrice && $price->getValue() !== false) {
                    if (null === $this->maxValue) {
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
