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
     * @var float
     */
    protected $baseAmount;

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        $priceComposite = $this->salableItem->getPriceInfo()->getPriceComposite();
        foreach (array_diff($priceComposite->getPriceCodes(), array($this->priceType)) as $priceCode) {
            $price = $this->salableItem->getPriceInfo()->getPrice($priceCode);
            if ($price instanceof OriginPrice && $price->getValue() !== false) {
                if (null === $this->baseAmount) {
                    $this->baseAmount = $price->getValue();
                } else {
                    $this->baseAmount = min($price->getValue(), $this->baseAmount);
                }
            }
        }
        return $this->baseAmount;
    }
}
