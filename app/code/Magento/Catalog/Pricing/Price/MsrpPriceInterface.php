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

use Magento\Framework\Pricing\Object\SaleableInterface;

/**
 * MSRP price interface
 */
interface MsrpPriceInterface
{
    /**
     * Price type MSRP
     */
    const PRICE_TYPE_MSRP = 'msrp_price';

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
     * @param SaleableInterface $product
     * @return bool
     */
    public function canApplyMsrp(SaleableInterface $product);
}
