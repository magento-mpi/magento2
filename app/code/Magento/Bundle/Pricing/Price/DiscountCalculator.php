<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;

/**
 * Class DiscountCalculator
 */
class DiscountCalculator
{
    /**
     * Apply percentage discount
     *
     * @param Product $product
     * @param float|null $value
     * @return float|null
     */
    public function calculateDiscount(Product $product, $value = null)
    {
        if (!$value) {
            $value = $product->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE)->getValue();
        }

        $discount = null;
        foreach ($product->getPriceInfo()->getPrices() as $price) {
            if ($price instanceof DiscountProviderInterface && $price->getDiscountPercent()) {
                $discount = min($price->getDiscountPercent(), $discount ?: $price->getDiscountPercent());
            }
        }
        return (null !== $discount) ?  $discount/100 * $value : $value;
    }
}
