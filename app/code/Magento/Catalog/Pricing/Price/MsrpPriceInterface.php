<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Catalog\Model\Product;

/**
 * MSRP price interface
 */
interface MsrpPriceInterface
{
    /**
     * Check is product need gesture to show price
     *
     * @return bool
     */
    public function isShowPriceOnGesture();

    /**
     * Get MAP message for price
     *
     * @return string
     */
    public function getMsrpPriceMessage();

    /**
     * Returns true in case MSRP is enabled
     *
     * @return bool
     */
    public function isMsrpEnabled();

    /**
     * Check if can apply Minimum Advertise price to product in specific visibility
     *
     * @param Product $saleableItem
     * @return bool
     */
    public function canApplyMsrp(Product $saleableItem);
}
