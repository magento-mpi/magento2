<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

/**
 * Bundle base price interface
 */
interface BasePriceInterface
{
    /**
     * Calculate base price for passed regular one
     *
     * @param float $price
     * @return float
     */
    public function calculateBaseValue($price);
}
