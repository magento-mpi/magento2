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
use Magento\Bundle\Pricing\Price;

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
        /** @var Price\BundleOptionPrice $optionPrice */
        $optionPrice = $this->getPriceType(Price\BundleOptionPriceInterface::PRICE_TYPE_BUNDLE_OPTION);
        return $optionPrice->getValue() !== $optionPrice->getMaxValue();
    }
}
