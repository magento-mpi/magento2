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
     * @return string
     */
    public function getAdjustmentCode();

    /**
     * @return bool
     */
    public function isIncludedInBasePrice();

    /**
     * @return bool
     */
    public function isIncludedInDisplayPrice();

    /**
     * @param float $amount
     * @param SaleableInterface $saleableItem
     * @return float
     */
    public function extractAdjustment($amount, SaleableInterface $saleableItem);

    /**
     * @param float $amount
     * @param SaleableInterface $saleableItem
     * @return float
     */
    public function applyAdjustment($amount, SaleableInterface $saleableItem);

    /**
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
