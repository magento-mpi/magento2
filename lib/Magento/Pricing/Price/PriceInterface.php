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

/**
 * Catalog price interface
 */
interface PriceInterface
{
    /**
     * Get price value
     *
     * Returns float if price value exists and false if not
     *
     * @return float|false
     */
    public function getValue();

    /**
     * @param float $baseAmount
     * @param string|null $excludedCode
     * @return float
     */
    public function getDisplayValue($baseAmount = null, $excludedCode = null);
}
