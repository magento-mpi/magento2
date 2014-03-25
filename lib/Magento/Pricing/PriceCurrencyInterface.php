<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing;

/**
 * Interface PriceCurrencyInterface
 */
interface PriceCurrencyInterface
{
    /**
     * Default precision
     */
    const DEFAULT_PRECISION = 2;

    /**
     * Convert price value
     *
     * @param float $amount
     * @param null|string|bool|int|\Magento\Core\Model\Store $store
     * @param \Magento\Directory\Model\Currency|string|null $currency
     * @return float
     */
    public function convert($amount, $store = null, $currency = null);

    /**
     * Format price value
     *
     * @param float $amount
     * @param bool $includeContainer
     * @param int $precision
     * @param null|string|bool|int|\Magento\Core\Model\Store $store
     * @param \Magento\Directory\Model\Currency|string|null $currency
     * @return float
     */
    public function format(
        $amount,
        $includeContainer = true,
        $precision = self::DEFAULT_PRECISION,
        $store = null,
        $currency = null
    );

    /**
     * Convert and format price value
     *
     * @param float $amount
     * @param bool $includeContainer
     * @param int $precision
     * @param null|string|bool|int|\Magento\Core\Model\Store $store
     * @param \Magento\Directory\Model\Currency|string|null $currency
     * @return string
     */
    public function convertAndFormat(
        $amount,
        $includeContainer = true,
        $precision = self::DEFAULT_PRECISION,
        $store = null,
        $currency = null
    );
}
