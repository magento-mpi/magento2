<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Directory\Model;

use Magento\Framework\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Integration test for \Magento\Directory\Model\Observer
 *
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ObjectManager */
    protected $objectManager;

    /** @var Observer */
    protected $observer;

    /** @var \Magento\Framework\App\MutableScopeConfig */
    protected $scopeConfig;

    /** @var string */
    protected $baseCurrency = 'USD';

    /** @var string */
    protected $baseCurrencyPath = 'currency/options/base';

    /** @var string */
    protected $allowedCurrenciesPath = 'currency/options/allow';

    /** @var \Magento\Core\Model\Resource\Config */
    protected $configResource;

    public function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->scopeConfig = $this->objectManager->create('Magento\Framework\App\MutableScopeConfig');
        $this->scopeConfig->setValue(Observer::IMPORT_ENABLE, 1, ScopeInterface::SCOPE_STORE);
        $this->scopeConfig->setValue(Observer::CRON_STRING_PATH, 'cron-string-path', ScopeInterface::SCOPE_STORE);
        $this->scopeConfig->setValue(Observer::IMPORT_SERVICE, 'webservicex', ScopeInterface::SCOPE_STORE);

        $this->configResource = $this->objectManager->get('Magento\Core\Model\Resource\Config');
        $this->configResource->saveConfig(
            $this->baseCurrencyPath,
            $this->baseCurrency,
            ScopeInterface::SCOPE_STORE,
            0
        );

        $this->observer = $this->objectManager->create('Magento\Directory\Model\Observer');
    }

    public function testScheduledUpdateCurrencyRates()
    {
        $allowedCurrencies = 'USD,GBP,EUR';
        $this->configResource->saveConfig(
            $this->allowedCurrenciesPath,
            $allowedCurrencies,
            ScopeInterface::SCOPE_STORE,
            0
        );
        $this->observer->scheduledUpdateCurrencyRates(null);
        $currencyResource = $this->objectManager
            ->create('Magento\Directory\Model\CurrencyFactory')
            ->create()
            ->getResource();
        $rates = $currencyResource->getCurrencyRates($this->baseCurrency, explode(',', $allowedCurrencies));
        $this->assertEquals(3, count($rates));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testScheduledUpdateCurrencyRates_invalidCurrency()
    {
        $allowedCurrencies = 'USD,GBP,XXX';
        $this->configResource->saveConfig(
            $this->allowedCurrenciesPath,
            $allowedCurrencies,
            ScopeInterface::SCOPE_STORE,
            0
        );
        $this->observer->scheduledUpdateCurrencyRates(null);
    }
}