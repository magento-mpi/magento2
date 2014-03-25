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
class BasePrice extends \Magento\Catalog\Pricing\Price\Price
{
    /**
     * Price type identifier string
     */
    const PRICE_TYPE = 'base_price';

    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE;

    /**
     * @var float|null
     */
    protected $baseAmount = null;

    /**
     * @return float|null
     */
    public function getValue()
    {
        $priceComposite = $this->salableItem->getPriceInfo()->getPriceComposite();
        foreach (array_diff($priceComposite->getPriceCodes(), array($this->priceType)) as $priceCode) {
            $price = $this->salableItem->getPriceInfo()->getPrice($priceCode);
            if ($price instanceof \Magento\Catalog\Pricing\Price\OriginPrice && $price->getValue() !== false) {
                if (is_null($this->baseAmount)) {
                    $this->baseAmount = $price->getValue();
                } else {
                    $this->baseAmount = min($price->getValue(), $this->baseAmount);
                }
            }
        }
        return $this->baseAmount;
    }
}
