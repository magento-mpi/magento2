<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Adjustment;

use Magento\Pricing\Object\SaleableInterface;

/**
 * Interface AdjustmentInterface
 */
interface AdjustmentInterface
{
    /**
     * Get adjustment code
     * (as declared in DI configuration)
     *
     * @return string
     */
    public function getAdjustmentCode();

    /**
     * Define if adjustment is included in base price
     *
     * @return bool
     */
    public function isIncludedInBasePrice();

    /**
     * Define if adjustment is included in display price
     *
     * @return bool
     */
    public function isIncludedInDisplayPrice();

    /**
     * Extract adjustment amount from the given amount value
     *
     * @param float $amount
     * @param SaleableInterface $saleableItem
     * @return float
     */
    public function extractAdjustment($amount, SaleableInterface $saleableItem);

    /**
     * Apply adjustment amount and return result value
     *
     * @param float $amount
     * @param SaleableInterface $saleableItem
     * @return float
     */
    public function applyAdjustment($amount, SaleableInterface $saleableItem);

    /**
     * Check if adjustment should be excluded from calculations along with the given adjustment
     *
     * @param string $adjustmentCode
     * @return bool
     */
    public function isExcludedWith($adjustmentCode);

    /**
     * Return sort order position
     *
     * @return int
     */
    public function getSortOrder();
}
