<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Render;

use Magento\Catalog\Pricing\Render as CatalogRender;
use Magento\Catalog\Pricing\Price;

/**
 * Class for final_price rendering
 */
class FinalPriceBox extends CatalogRender\FinalPriceBox
{
    /**
     * Check if bundle product has one more custom option with different prices
     *
     * @return bool
     */
    public function showRangePrice()
    {
        /** @var \Magento\Bundle\Pricing\Price\BasePrice $basePrice */
        $basePrice = $this->getPriceType(Price\BasePrice::PRICE_TYPE_BASE_PRICE);
        return $basePrice->getValue() !== $basePrice->getMaxValue();
    }
}
