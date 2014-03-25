<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Price;

use Magento\Pricing\Adjustment\AdjustmentInterface;

/**
 * Catalog price interface
 */
interface PriceInterface
{
    /**
     * @return float
     */
    public function getQuantity();

    /**
     * Get price unique identifier
     *
     * @return string
     */
    public function getPriceType();

    /**
     * @return float
     */
    public function getValue();

    /**
     * @param float $baseAmount
     * @param string|null $excludedCode
     * @return float
     */
    public function getDisplayValue($baseAmount = null, $excludedCode = null);

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return float
     */
    public function getBaseAmount();

    /**
     * @return float
     */
    public function getTotalAdjustmentAmount();

    /**
     * @return AdjustmentInterface[]
     */
    public function getAdjustments();

    /**
     * @param string $adjustmentCode
     * @return AdjustmentInterface
     */
    public function getAdjustment($adjustmentCode);

    /**
     * @param string $adjustmentCode
     * @return boolean
     */
    public function hasAdjustment($adjustmentCode);
}
