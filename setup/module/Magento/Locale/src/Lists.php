<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Locale;

class Lists
{
    /**
     * @var Data\Country
     */
    protected $country;

    /**
     * @var Data\Currency
     */
    protected $currency;

    /**
     * @var Data\Language
     */
    protected $language;

    /**
     * @var Locale
     */
    protected $locale;

    /**
     * @var Data\Timezone
     */
    protected $timezone;

    /**
     * @param Data\Country $country
     * @param Data\Currency $currency
     * @param Data\Language $language
     * @param Data\Timezone $timezone
     * @param Data\Locale $locale
     */
    public function __construct(
        Data\Country $country,
        Data\Currency $currency,
        Data\Language $language,
        Data\Timezone $timezone,
        Data\Locale $locale
    ) {
        $this->country = $country;
        $this->currency = $currency;
        $this->language = $language;
        $this->timezone = $timezone;
        $this->locale = $locale;
    }

    /**
     * Retrieve list of timezones
     *
     * @return array
     */
    public function getTimezoneList()
    {
        $list = [];
        foreach ($this->timezone->getData() as $code => $value) {
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
        $list = $this->currency->getData();
        foreach ($this->currency->getData() as $code => $value) {
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
        $languages = $this->language->getData();
        $countries = $this->country->getData();

        $list = [];
        foreach ($this->locale->getData() as $code) {
            if (strstr($code, '_')) {
                $data = explode('_', $code);
                if (!isset($languages[$data[0]]) || !isset($countries[$data[1]])) {
                    continue;
                }
                $list[$code] = $languages[$data[0]] . ' (' . $countries[$data[1]] . ')';
            }
        }
        asort($list);
        return $list;
    }
}
