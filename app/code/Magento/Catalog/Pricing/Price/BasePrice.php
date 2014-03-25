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

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {

        if (!$this->value) {
            $priceComposite = $this->salableItem->getPriceInfo()->getPriceComposite();
            $priceCodes = array_diff($priceComposite->getPriceCodes(), array(BasePrice::PRICE_TYPE_BASE_PRICE, FinalPrice::PRICE_TYPE_FINAL, MsrpPrice::PRICE_TYPE_MSRP));
            foreach ($priceCodes as $priceCode) {
                $price = $this->salableItem->getPriceInfo()->getPrice($priceCode);
                if ($price instanceof OriginPrice && $price->getValue() !== false) {
                    if (false === $this->value) {
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
