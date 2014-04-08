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

use Magento\Pricing\Amount\AmountInterface;

/**
 * Catalog price interface
 */
interface PriceInterface
{
    /**
     * Get price type code
     *
     * @return string
     */
    public function getPriceType();

    /**
     * Get price value
     *
     * @return float
     */
    public function getValue();

    /**
     * Get Price Amount object
     *
     * @return AmountInterface
     */
    public function getAmount();

    /**
     * Get Custom Amount object
     * (specify adjustment code to exclude)
     *
     * @param float $amount
     * @param null|bool|string $exclude
     * @return AmountInterface
     */
    public function getCustomAmount($amount = null, $exclude = null);
}
