<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ups\Model;

class CarrierTest extends \PHPUnit_Framework_TestCase
{
    const FREE_METHOD_NAME = 'free_method';

    const PAID_METHOD_NAME = 'paid_method';

    /**
     * Model under test
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * Model under test
     *
     * @var \Magento\Ups\Model\Carrier
     */
    protected $model;

    protected function setUp()
    {
        $this->config = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface');
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $helper->getObject(
            '\Magento\Ups\Model\Carrier',
            array('scopeConfig'=>$this->config)
        );
    }

    /**
     * @dataProvider getMethodPriceProvider
     * @param int $cost
     * @param string $shippingMethod
     * @param bool $freeShippingEnabled
     * @param int $freeShippingSubtotal
     * @param int $requestSubtotal
     * @param int $expectedPrice
     * @covers Magento\Shipping\Model\Carrier\AbstractCarrierOnline::getMethodPrice
     */
    public function testGetMethodPrice(
        $cost,
        $shippingMethod,
        $freeShippingEnabled,
        $freeShippingSubtotal,
        $requestSubtotal,
        $expectedPrice
    ) {
        $path = 'carriers/' . $this->model->getCarrierCode() . '/';
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
