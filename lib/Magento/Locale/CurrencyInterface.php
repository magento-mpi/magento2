<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Locale;

interface CurrencyInterface
{
    /**
     * Default currency
     */
    const DEFAULT_CURRENCY  = 'USD';

    /**
     * XML path to installed currencies
     */
    const XML_PATH_ALLOW_CURRENCIES_INSTALLED = 'system/currency/installed';

    /**
     * Retrieve currency code
     *
     * @return string
     */
    public function getDefaultCurrency();

    /**
     * Create \Magento\Currency object for current locale
     *
     * @param   string $currency
     * @return  \Magento\Currency
     */
    public function getCurrency($currency);
}
