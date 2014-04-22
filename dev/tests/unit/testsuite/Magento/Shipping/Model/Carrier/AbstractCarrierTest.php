<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shipping\Model\Carrier;

class AbstractCarrierOnlineTest extends \PHPUnit_Framework_TestCase
{
    const CODE = 'abstract';

    const FREE_METHOD_NAME = 'free_method';

    const PAID_METHOD_NAME = 'paid_method';

    /**
     * Model under test
     *
     * @var \Magento\App\Config\ScopeConfigInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * Model under test
     *
     * @var \Magento\Shipping\Model\Carrier\AbstractCarrierOnline|PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    protected function setUp()
    {
        $this->config = $this->getMock('\Magento\App\Config\ScopeConfigInterface'/*, array(), array(), '', false*/);
        $rateErrorFactory = $this->getMock(
            '\Magento\Sales\Model\Quote\Address\RateResult\ErrorFactory',
            array(),
            array(),
            '',
            false
        );
        $logFactory = $this->getMock('\Magento\Logger\AdapterFactory', array(), array(), '', false);
        $xmlElFactory = $this->getMock('\Magento\Shipping\Model\Simplexml\ElementFactory', array(), array(), '', false);
        $rateFactory = $this->getMock('\Magento\Shipping\Model\Rate\ResultFactory', array(), array(), '', false);
        $rateMethodFactory = $this->getMock(
            '\Magento\Sales\Model\Quote\Address\RateResult\MethodFactory',
            array(),
            array(),
            '',
            false
        );
        $trackFactory = $this->getMock('\Magento\Shipping\Model\Tracking\ResultFactory', array(), array(), '', false);
        $trackErrorFactory = $this->getMock(
            '\Magento\Shipping\Model\Tracking\Result\ErrorFactory',
            array(),
            array(),
            '',
            false
        );
        $trackStatusFactory = $this->getMock(
            '\Magento\Shipping\Model\Tracking\Result\StatusFactory',
            array(),
            array(),
            '',
            false
        );
        $regionFactory = $this->getMock('\Magento\Directory\Model\RegionFactory', array(), array(), '', false);
        $countryFactory = $this->getMock('\Magento\Directory\Model\CountryFactory', array(), array(), '', false);
        $currencyFactory = $this->getMock('\Magento\Directory\Model\CurrencyFactory', array(), array(), '', false);
        $directoryData = $this->getMock('\Magento\Directory\Helper\Data', array(), array(), '', false);
        $arguments = array(
            'scopeConfig'       => $this->config,
            'rateErrorFactory'  => $rateErrorFactory,
            'logAdapterFactory' => $logFactory,
            'xmlElFactory' => $xmlElFactory,
            'rateFactory' => $rateFactory,
            'rateMethodFactory' => $rateMethodFactory,
            'trackFactory' => $trackFactory,
            'trackErrorFactory' => $trackErrorFactory,
            'trackStatusFactory' => $trackStatusFactory,
            'regionFactory' => $regionFactory,
            'countryFactory' => $countryFactory,
            'currencyFactory' => $currencyFactory,
            'directoryData' => $directoryData
        );
        $this->model = $this->getMockForAbstractClass(
            'Magento\Shipping\Model\Carrier\AbstractCarrierOnline',
            $arguments
        );

        $property = new \ReflectionProperty('Magento\Shipping\Model\Carrier\AbstractCarrierOnline', '_code');
        $property->setAccessible(true);
        $property->setValue($this->model, self::CODE);
    }

    /**
     * @dataProvider getMethodPriceProvider
     * @param int $cost
     * @param string $shippingMethod
     * @param bool $freeShippingEnabled
     * @param int $freeShippingSubtotal
     * @param int $requestSubtotal
     * @param int $expectedPrice
     */
    public function testGetMethodPrice(
        $cost,
        $shippingMethod,
        $freeShippingEnabled,
        $freeShippingSubtotal,
        $requestSubtotal,
        $expectedPrice
    ) {
        $path = 'carriers/' . self::CODE . '/';
        $this->config->expects($this->any())->method('isSetFlag')->with($path . 'free_shipping_enable')->will(
            $this->returnValue($freeShippingEnabled)
        );
        $this->config->expects($this->any())->method('getValue')->will($this->returnValueMap(array(
            array(
                $path . 'free_method',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                null,
                self::FREE_METHOD_NAME
            ),
            array(
                $path . 'free_shipping_subtotal',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                null,
                $freeShippingSubtotal
            )
        )));
        $request = new \Magento\Sales\Model\Quote\Address\RateRequest();
        $request->setBaseSubtotalInclTax($requestSubtotal);
        $this->model->setRawRequest($request);
        $price = $this->model->getMethodPrice($cost, $shippingMethod);
        $this->assertEquals($expectedPrice, $price);
    }

    /**
     * Data provider for testGenerate method
     *
     * @return array
     */
    public function getMethodPriceProvider()
    {
        return array(
            array(3, self::FREE_METHOD_NAME, true, 5, 6, 0),
            array(3, self::FREE_METHOD_NAME, true, 5, 4, 3),
            array(3, self::FREE_METHOD_NAME, false, 5, 6, 3),
            array(3, self::FREE_METHOD_NAME, false, 5, 4, 3),
            array(3, self::PAID_METHOD_NAME, true, 5, 6, 3),
            array(3, self::PAID_METHOD_NAME, true, 5, 4, 3),
            array(3, self::PAID_METHOD_NAME, false, 5, 6, 3),
            array(3, self::PAID_METHOD_NAME, false, 5, 4, 3),
            array(7, self::FREE_METHOD_NAME, true, 5, 6, 0),
            array(7, self::FREE_METHOD_NAME, true, 5, 4, 7),
            array(7, self::FREE_METHOD_NAME, false, 5, 6, 7),
            array(7, self::FREE_METHOD_NAME, false, 5, 4, 7),
            array(7, self::PAID_METHOD_NAME, true, 5, 6, 7),
            array(7, self::PAID_METHOD_NAME, true, 5, 4, 7),
            array(7, self::PAID_METHOD_NAME, false, 5, 6, 7),
            array(7, self::PAID_METHOD_NAME, false, 5, 4, 7),
            array(3, self::FREE_METHOD_NAME, true, 5, 0, 3),
            array(3, self::FREE_METHOD_NAME, true, 5, 0, 3),
            array(3, self::FREE_METHOD_NAME, false, 5, 0, 3),
            array(3, self::FREE_METHOD_NAME, false, 5, 0, 3),
            array(3, self::PAID_METHOD_NAME, true, 5, 0, 3),
            array(3, self::PAID_METHOD_NAME, true, 5, 0, 3),
            array(3, self::PAID_METHOD_NAME, false, 5, 0, 3),
            array(3, self::PAID_METHOD_NAME, false, 5, 0, 3)
        );
    }

}
