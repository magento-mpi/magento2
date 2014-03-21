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
     * @param SaleableInterface $object
     * @return bool
     */
    public function isIncludedInDisplayPrice(SaleableInterface $object);

    /**
     * @param float $amount
     * @param SaleableInterface $object
     * @return float
     */
    public function extractAdjustment($amount, SaleableInterface $object);

    /**
     * @param float $amount
     * @param SaleableInterface $object
     * @return float
     */
    public function applyAdjustment($amount, SaleableInterface $object);

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
