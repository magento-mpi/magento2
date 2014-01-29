<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locale model interface
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model;

interface LocaleInterface
{
    /**
     * Default locale name
     */
    const DEFAULT_LOCALE    = 'en_US';
    const DEFAULT_TIMEZONE  = 'UTC';
    const DEFAULT_CURRENCY  = 'USD';

    /**
     * XML path constants
     */
    const XML_PATH_DEFAULT_LOCALE   = 'general/locale/code';
    const XML_PATH_DEFAULT_TIMEZONE = 'general/locale/timezone';
    const XML_PATH_ALLOW_CURRENCIES_INSTALLED = 'system/currency/installed';

    /**
     * Date and time format codes
     */
    const FORMAT_TYPE_FULL  = 'full';
    const FORMAT_TYPE_LONG  = 'long';
    const FORMAT_TYPE_MEDIUM= 'medium';
    const FORMAT_TYPE_SHORT = 'short';

    /**
     * Set default locale code
     *
     * @param   string $locale
     * @return  \Magento\Core\Model\LocaleInterface
     */
    public function setDefaultLocale($locale);

    /**
     * Retrieve default locale code
     *
     * @return string
     */
    public function getDefaultLocale();

    /**
     * Set locale
     *
     * @param   string $locale
     * @return  \Magento\Core\Model\LocaleInterface
     */
    public function setLocale($locale = null);

    /**
     * Retrieve timezone code
     *
     * @return string
     */
    public function getTimezone();

    /**
     * Retrieve currency code
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Retrieve locale object
     *
     * @return \Zend_Locale
     */
    public function getLocale();

    /**
     * Retrieve locale code
     *
     * @return string
     */
    public function getLocaleCode();

    /**
     * Specify current locale code
     *
     * @param   string $code
     * @return  \Magento\Core\Model\LocaleInterface
     */
    public function setLocaleCode($code);

    /**
     * Get options array for locale dropdown in current locale
     *
     * @return array
     */
    public function getOptionLocales();

    /**
     * Get translated to original locale options array for locale dropdown
     *
     * @return array
     */
    public function getTranslatedOptionLocales();

    /**
     * Retrieve timezone option list
     *
     * @return array
     */
    public function getOptionTimezones();

    /**
     * Retrieve days of week option list
     *
     * @param bool $preserveCodes
     * @param bool $ucFirstCode
     * @return array
     */
    public function getOptionWeekdays($preserveCodes = false, $ucFirstCode = false);

    /**
     * Retrieve country option list
     *
     * @return array
     */
    public function getOptionCountries();

    /**
     * Retrieve currency option list
     *
     * @return unknown
     */
    public function getOptionCurrencies();

    /**
     * Retrieve all currency option list
     *
     * @return unknown
     */
    public function getOptionAllCurrencies();

    /**
     * Retrieve array of allowed locales
     *
     * @return array
     */
    public function getAllowLocales();

    /**
     * Retrieve array of allowed currencies
     *
     * @return unknown
     */
    public function getAllowCurrencies();

    /**
     * Retrieve ISO date format
     *
     * @param   string $type
     * @return  string
     */
    public function getDateFormat($type=null);

    /**
     * Retrieve short date format with 4-digit year
     *
     * @return  string
     */
    public function getDateFormatWithLongYear();

    /**
     * Retrieve ISO time format
     *
     * @param   string $type
     * @return  string
     */
    public function getTimeFormat($type=null);

    /**
     * Retrieve ISO datetime format
     *
     * @param   string $type
     * @return  string
     */
    public function getDateTimeFormat($type);

    /**
     * Create \Zend_Date object for current locale
     *
     * @param mixed              $date
     * @param string             $part
     * @param string|Zend_Locale $locale
     * @param bool               $useTimezone
     * @return \Zend_Date
     */
    public function date($date = null, $part = null, $locale = null, $useTimezone = true);

    /**
     * Create \Zend_Date object with date converted to store timezone and store Locale
     *
     * @param   mixed $store Information about store
     * @param   string|integer|Zend_Date|array|null $date date in UTC
     * @param   boolean $includeTime flag for including time to date
     * @return  \Zend_Date
     */
    public function storeDate($store=null, $date=null, $includeTime=false);

    /**
     * Create \Zend_Date object with date converted from store's timezone
     * to UTC time zone. Date can be passed in format of store's locale
     * or in format which was passed as parameter.
     *
     * @param mixed $store Information about store
     * @param string|integer|Zend_Date|array|null $date date in store's timezone
     * @param boolean $includeTime flag for including time to date
     * @param null|string $format
     * @return \Zend_Date
     */
    public function utcDate($store, $date, $includeTime = false, $format = null);

    /**
     * Get store timestamp
     * Timestamp will be built with store timezone settings
     *
     * @param   mixed $store
     * @return  int
     */
    public function storeTimeStamp($store=null);

    /**
     * Create \Zend_Currency object for current locale
     *
     * @param   string $currency
     * @return  \Zend_Currency
     */
    public function currency($currency);

    /**
     * Returns the first found number from an string
     * Parsing depends on given locale (grouping and decimal)
     *
     * Examples for input:
     * '  2345.4356,1234' = 23455456.1234
     * '+23,3452.123' = 233452.123
     * ' 12343 ' = 12343
     * '-9456km' = -9456
     * '0' = 0
     * '2 054,10' = 2054.1
     * '2'054.52' = 2054.52
     * '2,46 GB' = 2.46
     *
     * @param string|float|int $value
     * @return float|null
     */
    public function getNumber($value);

    /**
     * Functions returns array with price formatting info for js function
     * formatCurrency in js/varien/js.js
     *
     * @return array
     */
    public function getJsPriceFormat();

    /**
     * Push current locale to stack and replace with locale from specified store
     * Event is not dispatched.
     *
     * @param int $storeId
     */
    public function emulate($storeId);

    /**
     * Get last locale, used before last emulation
     *
     */
    public function revert();

    /**
     * Returns localized informations as array, supported are several
     * types of informations.
     * For detailed information about the types look into the documentation
     *
     * @param  string             $path   (Optional) Type of information to return
     * @param  string             $value  (Optional) Value for detail list
     * @return array Array with the wished information in the given language
     */
    public function getTranslationList($path = null, $value = null);

    /**
     * Returns a localized information string, supported are several types of informations.
     * For detailed information about the types look into the documentation
     *
     * @param  string             $value  Name to get detailed information about
     * @param  string             $path   (Optional) Type of information to return
     * @return string|false The wished information in the given language
     */
    public function getTranslation($value = null, $path = null);

    /**
     * Returns the localized country name
     *
     * @param  string             $value  Name to get detailed information about
     * @return array
     */
    public function getCountryTranslation($value);

    /**
     * Returns an array with the name of all countries translated to the given language
     *
     * @return array
     */
    public function getCountryTranslationList();

    /**
     * Checks if current date of the given store (in the store timezone) is within the range
     *
     * @param int|string|\Magento\Core\Model\Store $store
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return bool
     */
    public function isStoreDateInInterval($store, $dateFrom = null, $dateTo = null);

    /**
     * Format date using current locale options and time zone.
     *
     * @param Zend_Date|null $date
     * @param string $format
     * @param bool $showTime
     * @return string
     */
    public function formatDate(
        $date = null, $format = \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT, $showTime = false
    );

    /**
     * Format time using current locale options
     *
     * @param Zend_Date|null $time
     * @param string $format
     * @param bool $showDate
     * @return string
     */
    public function formatTime(
        $time = null, $format = \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT, $showDate = false
    );

    /**
     * Gets the store config timezone
     *
     * @return string
     */
    public function getConfigTimezone();
}
