<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Model\System;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test for Magento\CurrencySymbol\Model\System\Currencysymbol
 *
 * @magentoAppArea adminhtml
 */
class CurrencysymbolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CurrencySymbol\Model\System\Currencysymbol
     */
    protected $currencySymbolModel;

    protected function setUp()
    {
        $this->currencySymbolModel = Bootstrap::getObjectManager()->create(
            'Magento\CurrencySymbol\Model\System\Currencysymbol'
        );
    }

    protected function tearDown()
    {
        $this->currencySymbolModel = null;
        Bootstrap::getObjectManager()->get('Magento\Framework\App\Config\ReinitableConfigInterface')->reinit();
        Bootstrap::getObjectManager()->create('Magento\Framework\StoreManagerInterface')->reinitStores();
    }

    public function testGetCurrencySymbolsData()
    {
        $currencySymbolsData = $this->currencySymbolModel->getCurrencySymbolsData();
        $this->assertArrayHasKey('USD', $currencySymbolsData, 'Default currency option for USD is missing.');
        $this->assertArrayHasKey('EUR', $currencySymbolsData, 'Default currency option for EUR is missing.');
    }

    public function testSetEmptyCurrencySymbolsData()
    {
        $currencySymbolsDataBefore = $this->currencySymbolModel->getCurrencySymbolsData();

        $this->currencySymbolModel->setCurrencySymbolsData([]);

        $currencySymbolsDataAfter = $this->currencySymbolModel->getCurrencySymbolsData();

        //Make sure symbol data is unchanged
        $this->assertEquals($currencySymbolsDataBefore, $currencySymbolsDataAfter);
    }

    public function testSetCurrencySymbolsData()
    {
        $currencySymbolsData = $this->currencySymbolModel->getCurrencySymbolsData();
        $this->assertArrayHasKey('EUR', $currencySymbolsData);

        //Change currency symbol
        $currencySymbolsData = [
            'EUR' => '@'
        ];
        $this->currencySymbolModel->setCurrencySymbolsData($currencySymbolsData);

        //Verify if the new symbol is set
        $this->assertEquals(
            '@',
            $this->currencySymbolModel->getCurrencySymbolsData()['EUR']['displaySymbol'],
            'Symbol not set correctly.'
        );

        $this->assertEquals('@', $this->currencySymbolModel->getCurrencySymbol('EUR'), 'Symbol not set correctly.');
    }

    /**
     * @depends testSetCurrencySymbolsData
     */
    public function testGetCurrencySymbol()
    {
        //dependency is added for now since tear down (or app isolation) is not helping clear the configuration data
        $this->assertEquals('@', $this->currencySymbolModel->getCurrencySymbol('EUR'));
    }

    public function testGetCurrencySymbolNonExistent()
    {
        $this->assertFalse($this->currencySymbolModel->getCurrencySymbol('AUD'));
    }
}
