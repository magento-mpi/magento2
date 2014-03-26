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
     * @return float
     */
    public function getValue();

    /**
     * @param float $baseAmount
     * @param string|null $excludedCode
     * @return float
     */
    public function getDisplayValue($baseAmount = null, $excludedCode = null);
}
