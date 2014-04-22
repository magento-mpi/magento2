<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing;

use Magento\Pricing\Adjustment\AdjustmentInterface;
use Magento\Pricing\Price\PriceInterface;

/**
 * Price info model interface
 */
interface PriceInfoInterface
{
    /**
     * Default product quantity
     */
    const PRODUCT_QUANTITY_DEFAULT = 1.;

    /**
     * Returns array of prices
     *
     * @return PriceInterface[]
     */
    public function getPrices();

    /**
     * Returns price by code
     *
     * @param string $priceCode
     * @return PriceInterface
     */
    public function getPrice($priceCode);

    /**
     * Get all registered adjustments
     *
     * @return AdjustmentInterface[]
     */
    public function getAdjustments();

    /**
     * Get adjustment by code
     *
     * @param string $adjustmentCode
     * @return AdjustmentInterface
     */
    public function getAdjustment($adjustmentCode);
}
