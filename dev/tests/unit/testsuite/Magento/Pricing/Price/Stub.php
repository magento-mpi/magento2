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
 * Class Stub for testing abstract class AbstractPrice
 *
 * @package Magento\Catalog\Pricing\Price
 */
class Stub extends AbstractPrice
{
    /**
     * Get price value
     *
     * @return float
     */
    public function getValue()
    {
        $examplePrice = 77.0;
        return $examplePrice;
    }
} 