<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Directory\Model;

class PriceCurrencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PriceCurrency
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $currencyFactory;

    public function setUp()
    {
        $this->storeManager = $this->getMockBuilder('Magento\Store\Model\StoreManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->currencyFactory = $this->getMockBuilder('Magento\Directory\Model\CurrencyFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->logger = $this->getMockBuilder('Magento\Framework\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->priceCurrency = $objectManager->getObject('Magento\Directory\Model\PriceCurrency', array(
            'storeManager' => $this->storeManager,
            'currencyFactory' => $this->currencyFactory
        ));
    }

    public function testConvert()
    {
        $amount = 5.6;
        $convertedAmount = 9.3;

        $currency = $this->getCurrentCurrencyMock();
        $baseCurrency = $this->getBaseCurrencyMock($amount, $convertedAmount, $currency);
        $store = $this->getStoreMock($baseCurrency);

        $this->assertEquals($convertedAmount, $this->priceCurrency->convert($amount, $store, $currency));
    }

    public function testConvertWithStoreCode()
    {
        $amount = 5.6;
        $storeCode = 2;
        $convertedAmount = 9.3;

        $currency = $this->getCurrentCurrencyMock();
        $baseCurrency = $this->getBaseCurrencyMock($amount, $convertedAmount, $currency);
        $store = $this->getStoreMock($baseCurrency);

        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->with($storeCode)
            ->will($this->returnValue($store));

        $this->assertEquals($convertedAmount, $this->priceCurrency->convert($amount, $storeCode, $currency));
    }

    public function testConvertWithCurrencyString()
    {
        $amount = 5.6;
        $currency = 'ru';
        $convertedAmount = 9.3;

        $currentCurrency = $this->getCurrentCurrencyMock();
        $currentCurrency->expects($this->once())
            ->method('load')
            ->with($currency)
            ->will($this->returnSelf());

        $this->currencyFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($currentCurrency));

        $baseCurrency = $this->getBaseCurrencyMock($amount, $convertedAmount, $currentCurrency);
        $baseCurrency->expects($this->once())
            ->method('getRate')
            ->with($currentCurrency)
            ->will($this->returnValue(1.2));
        $store = $this->getStoreMock($baseCurrency);

        $this->assertEquals($convertedAmount, $this->priceCurrency->convert($amount, $store, $currency));
    }

    public function testConvertWithStoreCurrency()
    {
        $amount = 5.6;
        $currency = null;
        $convertedAmount = 9.3;

        $currentCurrency = $this->getCurrentCurrencyMock();
        $baseCurrency = $this->getBaseCurrencyMock($amount, $convertedAmount, $currentCurrency);
        $store = $this->getStoreMock($baseCurrency);
        $store->expects($this->atLeastOnce())
            ->method('getCurrentCurrency')
            ->will($this->returnValue($currentCurrency));

        $this->assertEquals($convertedAmount, $this->priceCurrency->convert($amount, $store, $currency));
    }

    public function testFormat()
    {
        $amount = 5.6;
        $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION;
        $includeContainer = false;
        $store = null;
        $formattedAmount = '5.6 grn';

        $currency = $this->getCurrentCurrencyMock();
        $currency->expects($this->once())
            ->method('formatPrecision')
            ->with($amount, $precision, [], $includeContainer)
            ->will($this->returnValue($formattedAmount));

        $this->assertEquals($formattedAmount, $this->priceCurrency->format(
            $amount,
            $includeContainer,
            $precision,
            $store,
            $currency
        ));
    }

    public function testConvertAndFormat()
    {
        $amount = 5.6;
        $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION;
        $includeContainer = false;
        $store = null;
        $convertedAmount = 9.3;
        $formattedAmount = '9.3 grn';

        $currency = $this->getCurrentCurrencyMock();
        $baseCurrency = $this->getBaseCurrencyMock($amount, $convertedAmount, $currency);
        $store = $this->getStoreMock($baseCurrency);

        $currency->expects($this->once())
            ->method('formatPrecision')
            ->with($convertedAmount, $precision, [], $includeContainer)
            ->will($this->returnValue($formattedAmount));

        $this->assertEquals($formattedAmount, $this->priceCurrency->convertAndFormat(
            $amount,
            $includeContainer,
            $precision,
            $store,
            $currency
        ));
    }

    protected function getCurrentCurrencyMock()
    {
        $currency = $this->getMockBuilder('Magento\Directory\Model\Currency')
            ->disableOriginalConstructor()
            ->getMock();

        return $currency;
    }

    protected function getStoreMock($baseCurrency)
    {
        $store = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();

        $store->expects($this->atLeastOnce())
            ->method('getBaseCurrency')
            ->will($this->returnValue($baseCurrency));

        return $store;
    }

    protected function getBaseCurrencyMock($amount, $convertedAmount, $currency)
    {
        $baseCurrency = $this->getMockBuilder('Magento\Directory\Model\Currency')
            ->disableOriginalConstructor()
            ->getMock();

        $baseCurrency->expects($this->once())
            ->method('convert')
            ->with($amount, $currency)
            ->will($this->returnValue($convertedAmount));

        return $baseCurrency;
    }
}
