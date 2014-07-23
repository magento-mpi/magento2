<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * Currency data builder for quote
 *
 * @codeCoverageIgnore
 */
class CurrencyBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set global currency code
     *
     * @param string|null $value
     * @return $this
     */
    public function setGlobalCurrencyCode($value)
    {
        return $this->_set(Currency::GLOBAL_CURRENCY_CODE, $value);
    }

    /**
     * Set base currency code
     *
     * @param string|null $value
     * @return $this
     */
    public function setBaseCurrencyCode($value)
    {
        return $this->_set(Currency::BASE_CURRENCY_CODE, $value);
    }

    /**
     * Set store currency code
     *
     * @param string|null $value
     * @return $this
     */
    public function setStoreCurrencyCode($value)
    {
        return $this->_set(Currency::STORE_CURRENCY_CODE, $value);
    }

    /**
     * Set quote currency code
     *
     * @param string|null $value
     * @return $this
     */
    public function setQuoteCurrencyCode($value)
    {
        return $this->_set(Currency::QUOTE_CURRENCY_CODE, $value);
    }

    /**
     * Set store currency to base currency rate
     *
     * @param double|null $value
     * @return $this
     */
    public function setStoreToBaseRate($value)
    {
        return $this->_set(Currency::STORE_TO_BASE_RATE, $value);
    }

    /**
     * Set store currency to quote currency rate
     *
     * @param double|null $value
     * @return $this
     */
    public function setStoreToQuoteRate($value)
    {
        return $this->_set(Currency::STORE_TO_QUOTE_RATE, $value);
    }

    /**
     * Set base currency to global currency rate
     *
     * @param double|null $value
     * @return $this
     */
    public function setBaseToGlobalRate($value)
    {
        return $this->_set(Currency::BASE_TO_GLOBAL_RATE, $value);
    }

    /**
     * Set base currency to quote currency rate
     *
     * @param double|null $value
     * @return $this
     */
    public function setBaseToQuoteRate($value)
    {
        return $this->_set(Currency::BASE_TO_QUOTE_RATE, $value);
    }
}
