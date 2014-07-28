<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * Currency data for quote
 *
 * @codeCoverageIgnore
 */
class Currency extends \Magento\Framework\Service\Data\AbstractObject
{
    CONST GLOBAL_CURRENCY_CODE = 'global_currency_code';

    CONST BASE_CURRENCY_CODE = 'base_currency_code';

    CONST STORE_CURRENCY_CODE = 'store_currency_code';

    CONST QUOTE_CURRENCY_CODE = 'quote_currency_code';

    CONST STORE_TO_BASE_RATE = 'store_to_base_rate';

    CONST STORE_TO_QUOTE_RATE = 'store_to_quote_rate';

    CONST BASE_TO_GLOBAL_RATE = 'base_to_global_rate';

    CONST BASE_TO_QUOTE_RATE = 'base_to_quote_rate';

    /**
     * Get global currency code
     *
     * @return string|null
     */
    public function getGlobalCurrencyCode()
    {
        return $this->_get(self::GLOBAL_CURRENCY_CODE);
    }

    /**
     * Get base currency code
     *
     * @return string|null
     */
    public function getBaseCurrencyCode()
    {
        return $this->_get(self::BASE_CURRENCY_CODE);
    }

    /**
     * Get store currency code
     *
     * @return string|null
     */
    public function getStoreCurrencyCode()
    {
        return $this->_get(self::STORE_CURRENCY_CODE);
    }

    /**
     * Get quote currency code
     *
     * @return string|null
     */
    public function getQuoteCurrencyCode()
    {
        return $this->_get(self::QUOTE_CURRENCY_CODE);
    }

    /**
     * Get store currency to base currency rate
     *
     * @return double|null
     */
    public function getStoreToBaseRate()
    {
        return $this->_get(self::STORE_TO_BASE_RATE);
    }

    /**
     * Get store currency to quote currency rate
     *
     * @return double|null
     */
    public function getStoreToQuoteRate()
    {
        return $this->_get(self::STORE_TO_QUOTE_RATE);
    }

    /**
     * Get base currency to global currency rate
     *
     * @return double|null
     */
    public function getBaseToGlobalRate()
    {
        return $this->_get(self::BASE_TO_GLOBAL_RATE);
    }

    /**
     * Get base currency to quote currency rate
     *
     * @return double|null
     */
    public function getBaseToQuoteRate()
    {
        return $this->_get(self::BASE_TO_QUOTE_RATE);
    }
}
