<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pricing\Adjustment;

/**
 * Class CalculatorTest
 *
 * @package Magento\Pricing\Adjustment
 */
class CalculatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Pricing\Adjustment\Calculator
     */
    protected $model;

    /**
     * @var \Magento\Pricing\Amount\AmountFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $amountFactoryMock;

    public function setUp()
    {
        $this->amountFactoryMock = $this->getMockBuilder('Magento\Pricing\Amount\AmountFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = new \Magento\Pricing\Adjustment\Calculator($this->amountFactoryMock);
    }

    /**
     * Test getAmount()
     */
    public function testGetAmount()
    {
        $amount = 10;
        $fullAmount = $amount;
        $newAmount = 15;
        $taxAdjustmentCode = 'tax';
        $weeeAdjustmentCode = 'weee';
        $adjust = 5;
        $expectedAdjustments = [
            $taxAdjustmentCode => $adjust,
            $weeeAdjustmentCode => $adjust
        ];

        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['getPriceInfo', '__wakeup'])
            ->getMock();

        $taxAdjustmentMock = $this->getMockBuilder('Magento\Tax\Pricing\Adjustment')
            ->disableOriginalConstructor()
            ->getMock();
        $taxAdjustmentMock->expects($this->once())
            ->method('getAdjustmentCode')
            ->will($this->returnValue($taxAdjustmentCode));
        $taxAdjustmentMock->expects($this->once())
            ->method('isIncludedInBasePrice')
            ->will($this->returnValue(true));
        $taxAdjustmentMock->expects($this->once())
            ->method('extractAdjustment')
            ->with($this->equalTo($amount), $this->equalTo($productMock))
            ->will($this->returnValue($adjust));

        $weeeAdjustmentMock = $this->getMockBuilder('Magento\Weee\Pricing\Adjustment')
            ->disableOriginalConstructor()
            ->getMock();
        $weeeAdjustmentMock->expects($this->once())
            ->method('getAdjustmentCode')
            ->will($this->returnValue($weeeAdjustmentCode));
        $weeeAdjustmentMock->expects($this->once())
            ->method('isIncludedInBasePrice')
            ->will($this->returnValue(false));
        $weeeAdjustmentMock->expects($this->once())
            ->method('isIncludedInDisplayPrice')
            ->with($this->equalTo($productMock))
            ->will($this->returnValue(true));
        $weeeAdjustmentMock->expects($this->once())
            ->method('applyAdjustment')
            ->with($this->equalTo($fullAmount), $this->equalTo($productMock))
            ->will($this->returnValue($newAmount));

        $adjustments = [$taxAdjustmentMock, $weeeAdjustmentMock];

        $priceInfoMock = $this->getMockBuilder('\Magento\Pricing\PriceInfoInterface')
            ->disableOriginalConstructor()
            //->setMethods(['getPriceInfo'])
            ->getMock();
        $priceInfoMock->expects($this->any())
            ->method('getAdjustments')
            ->will($this->returnValue($adjustments));

        $productMock->expects($this->any())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfoMock));

        $amountBaseMock = $this->getMockBuilder('Magento\Pricing\Amount\Base')
            ->disableOriginalConstructor()
            ->getMock();

        $this->amountFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo($newAmount), $this->equalTo($expectedAdjustments))
            ->will($this->returnValue($amountBaseMock));
        $result = $this->model->getAmount($amount, $productMock);
        $this->assertInstanceOf('Magento\Pricing\Amount\AmountInterface', $result);
    }
}
