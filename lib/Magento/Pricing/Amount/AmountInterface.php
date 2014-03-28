<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Amount;

/**
 * Amount interface
 */
interface AmountInterface
{
    /**
     * @param null|string|array $exclude
     * @return float
     */
    public function getValue($exclude = null);

    /**
     * Return amount value in string format
     *
     * @return string
     */
    public function __toString();

    /**
     * @return float
     */
    public function getBaseAmount();

    /**
     * @param string $adjustmentCode
     * @return float
     */
    public function getAdjustmentAmount($adjustmentCode);

    /**
     * @return float
     */
    public function getTotalAdjustmentAmount();

    /**
     * @return float[]
     */
    public function getAdjustmentAmounts();

    /**
     * @param string $adjustmentCode
     * @return boolean
     */
    public function hasAdjustment($adjustmentCode);
}
