<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Model;

use Zend_Locale;

class Lists
{
    /**
     * Zend locale object
     *
     * @var Zend_Locale
     */
    protected $zendLocale;

    /**
     * Constructor
     *
     * @param Zend_Locale $zendLocale
     */
    public function __construct(Zend_Locale $zendLocale)
    {
        $this->zendLocale = $zendLocale;
    }

    /**
     * Retrieve list of timezones
     *
     * @return array
     */
    public function getTimezoneList()
    {
        $timeZone  = $this->zendLocale->getTranslationList('WindowsToTimezone');
        $list = [];
        foreach ($timeZone as $code => $value) {
            $list[$code] = $value . ' (' . $code . ')';
        }
        asort($list);
        return $list;
    }

    /**
     * Retrieve list of currencies
     *
     * @return array
     */
    public function getCurrencyList()
    {
        $currencies = $this->zendLocale->getTranslationList('NameToCurrency');
        $list = [];
        foreach ($currencies as $code => $value) {
            $list[$code] = $value . ' (' . $code . ')';
        }
        asort($list);
        return $list;
    }

    /**
     * Retrieve list of locales
     *
     * @return  array
     */
    public function getLocaleList()
    {
        $languages = $this->zendLocale->getTranslationList('Language');
        $countries = $this->zendLocale->getTranslationList('Territory');
        $locale = $this->zendLocale->getLocaleList();
        $list = [];
        foreach ($locale as $key => $value) {
            if (strstr($key, '_')) {
                $data = explode('_', $key);
                if (!isset($languages[$data[0]]) || !isset($countries[$data[1]])) {
                    continue;
                }
                $list[$key] = $languages[$data[0]] . ' (' . $countries[$data[1]] . ')';
            }
        }
        asort($list);
        return $list;
    }
}
