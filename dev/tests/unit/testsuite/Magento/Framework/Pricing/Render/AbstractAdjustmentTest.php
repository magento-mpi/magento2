<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Pricing\Render;

/**
 * Test class for \Magento\Framework\Pricing\Render\AbstractAdjustment
 */
class AbstractAdjustmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Amount
     */
    protected $model;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceCurrency;

    public function setUp()
    {
        $this->priceCurrency = $this->getMock('Magento\Framework\Pricing\PriceCurrencyInterface');

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $constructorArgs = $objectManager->getConstructArguments(
            'Magento\Framework\Pricing\Render\AbstractAdjustment',
            array('priceCurrency' => $this->priceCurrency)
        );
        $this->model = $this->getMockBuilder('Magento\Framework\Pricing\Render\AbstractAdjustment')
            ->setConstructorArgs($constructorArgs)
            ->getMockForAbstractClass();
    }

    public function testConvertAndFormatCurrency()
    {
        $amount = '100';
        $includeContainer = true;
        $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION;

        $result = '100.0 grn';

        $this->priceCurrency->expects($this->once())
            ->method('convertAndFormat')
            ->with($amount, $includeContainer, $precision)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->convertAndFormatCurrency($amount, $includeContainer, $precision));
    }
}
