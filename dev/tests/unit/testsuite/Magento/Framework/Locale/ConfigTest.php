<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Locale;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    private static $samplePresentLocales = [
         'en_US', 'lv_LV', 'pt_BR', 'it_IT', 'ar_EG', 'bg_BG', 'en_IE', 'es_ES',
         'en_AU', 'pt_PT', 'ru_RU', 'en_CA', 'vi_VN', 'ja_JP', 'en_GB', 'zh_CN',
         'zh_TW', 'fr_FR', 'ar_KW', 'pl_PL', 'ko_KR', 'sk_SK', 'el_GR', 'hi_IN',
    ];

    private static $sampleAbsentLocales = [
        'aa_BB', 'foo_BAR', 'cc_DD',
    ];

    private static $sampleAdditionalLocales = [
        'en-AA', 'es-ZZ',
    ];


    private static $samplePresentCurrencies = [
        'AUD', 'BBD', 'GBP', 'CAD', 'CZK', 'GQE', 'CNY', 'DJF', 'HKD', 'JPY', 'MYR',
        'MXN', 'NZD', 'PHP', 'SGD', 'CHF', 'TWD', 'USD', 'AED', 'ZWD', 'ROL', 'CHE',
    ];

    private static $sampleAbsentCurrencies = [
        'ABC', 'DEF', 'GHI', 'ZZZ',
    ];

    private static $sampleAdditionalCurrencies = [
        'QED', 'PNP', 'EJN', 'MTO', 'EBY',
    ];

    /** @var  \Magento\Framework\Locale\Config */
    private $configObject;


    public function testGetAllowedLocalesNoDataArray()
    {
        $this->configObject = new Config();

        $retrievedLocales = $this->configObject->getAllowedLocales();

        foreach($this::$samplePresentLocales as $presentLocale)
        {
            $this->assertContains($presentLocale, $retrievedLocales);
        }

        foreach($this::$sampleAbsentLocales as $absentLocale)
        {
            $this->assertNotContains($absentLocale, $retrievedLocales);
        }
    }

    public function testGetAllowedLocalesGivenDataArray()
    {
        $this->configObject = new Config(
            [
                'allowedLocales' => $this::$sampleAdditionalLocales,
            ]
        );

        $retrievedLocalesWithAdditions = $this->configObject->getAllowedLocales();

        foreach($this::$samplePresentLocales as $presentLocale)
        {
            $this->assertContains($presentLocale, $retrievedLocalesWithAdditions);
        }

        foreach($this::$sampleAbsentLocales as $absentLocale)
        {
            $this->assertNotContains($absentLocale, $retrievedLocalesWithAdditions);
        }

        foreach($this::$sampleAdditionalLocales as $additionalLocale)
        {
            $this->assertContains($additionalLocale, $retrievedLocalesWithAdditions);
        }
    }

    public function testGetAllowedCurrenciesNoDataArray()
    {
        $this->configObject = new Config();

        $retrievedCurrencies = $this->configObject->getAllowedCurrencies();

        foreach($this::$samplePresentCurrencies as $presentCurrency)
        {
            $this->assertContains($presentCurrency, $retrievedCurrencies);
        }

        foreach($this::$sampleAbsentCurrencies as $absentCurrency)
        {
            $this->assertNotContains($absentCurrency, $retrievedCurrencies);
        }
    }

    public function testGetAllowedCurrenciesGivenDataArray()
    {
        $this->configObject = new Config(
            [
                'allowedCurrencies' => $this::$sampleAdditionalCurrencies,
            ]
        );

        $retrievedCurrenciesWithAdditions = $this->configObject->getAllowedCurrencies();

        foreach($this::$samplePresentCurrencies as $presentCurrency)
        {
            $this->assertContains($presentCurrency, $retrievedCurrenciesWithAdditions);
        }

        foreach($this::$sampleAbsentCurrencies as $absentCurrency)
        {
            $this->assertNotContains($absentCurrency, $retrievedCurrenciesWithAdditions);
        }

        foreach($this::$sampleAdditionalCurrencies as $additionalCurrency)
        {
            $this->assertContains($additionalCurrency, $retrievedCurrenciesWithAdditions);
        }
    }
}