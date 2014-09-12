<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Math;

class CalculatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Math\Calculator
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject
     */
    protected $priceCurrency;

    public function setUp()
    {
        $this->priceCurrency = $this->getMockBuilder('Magento\Framework\Pricing\PriceCurrencyInterface')->getMock();
        $this->priceCurrency->expects($this->any())
            ->method('round')
            ->will($this->returnCallback(function ($argument) {
                return round($argument, 2);
            }));

        $this->_model = new \Magento\Framework\Math\Calculator($this->priceCurrency);
    }

    /**
     * @param float $price
     * @param bool $negative
     * @param float $expected
     * @dataProvider deltaRoundDataProvider
     * @covers \Magento\Framework\Math\Calculator::deltaRound
     * @covers \Magento\Framework\Math\Calculator::__construct
     */
    public function testDeltaRound($price, $negative, $expected)
    {
        $this->assertEquals($expected, $this->_model->deltaRound($price, $negative));
    }

    /**
     * @return array
     */
    public function deltaRoundDataProvider()
    {
        return array(
            array(0, false, 0),
            array(2.223, false, 2.22),
            array(2.226, false, 2.23),
            array(2.226, true, 2.23),
        );
    }
}
