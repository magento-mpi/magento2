<?php
/**
 * Created by PhpStorm.
 * User: rbates
 * Date: 7/14/14
 * Time: 12:07 PM
 */

namespace Magento\Tax\Model\Calculation;

class UnitBasedCalculatorTest extends \PHPUnit_Framework_TestCase
{

    const STORE_ID = 2300;

    /** @var \Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockTaxItemDetailsBuilder;

    /** @var \Magento\Tax\Model\Calculation | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockCalculationTool;

    /** @var \Magento\Tax\Model\Config | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockConfig;

    /** @var UnitBasedCalculator */
    protected $model;

    public function setUp()
    {
        $this->mockTaxItemDetailsBuilder = $this->getMockBuilder('\Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockCalculationTool = $this->getMockBuilder('\Magento\Tax\Model\Calculation')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockConfig = $this->getMockBuilder('\Magento\Tax\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new UnitBasedCalculator(
            $this->mockTaxItemDetailsBuilder,
            $this->mockCalculationTool,
            $this->mockConfig,
            self::STORE_ID
        );
    }

    public function testCalculateWithTaxInPrice()
    {
        /** @var $mockItem \Magento\Tax\Service\V1\Data\QuoteDetails\Item | \PHPUnit_Framework_MockObject_MockObject */
        $mockItem = $this->getMockBuilder('Magento\Tax\Service\V1\Data\QuoteDetails\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $mockItem->expects($this->once())
            ->method('getTaxIncluded')
            ->will($this->returnValue(true));

        $this->mockConfig->expects($this->once())
            ->method('crossBorderTradeEnabled')
            ->will($this->returnValue(false));
        
        $addressRateRequest = new \Magento\Framework\Object();

        $this->mockCalculationTool->expects($this->once())
            ->method('getRateRequest')
            ->withAnyParameters()
            ->will($this->returnValue($addressRateRequest));

        $mockAppliedTaxRateBuilder = $this->getMockBuilder('Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('getAppliedTaxBuilder')
            ->will($this->returnValue($mockAppliedTaxRateBuilder));
        
        $this->mockCalculationTool->expects($this->once())
            ->method('getAppliedRates')
            ->withAnyParameters()
            ->will($this->returnValue([]));

        $this->mockConfig->expects($this->once())
            ->method('applyTaxAfterDiscount')
            ->will($this->returnValue(true));

        $mockItem->expects($this->once())
            ->method('getDiscountAmount')
            ->will($this->returnValue(1));

        $this->mockCalculationTool->expects($this->once())
            ->method('getAppliedRates')
            ->with($addressRateRequest)
            ->will($this->returnValue([]));

        $expectedReturnValue = 'SOME RETURN OBJECT';
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('create')
            ->will($this->returnValue($expectedReturnValue));

        $this->assertSame($expectedReturnValue, $this->model->calculate($mockItem, 1));
    }

    public function testCalculateWithTaxNotInPrice()
    {
        /** @var $mockItem \Magento\Tax\Service\V1\Data\QuoteDetails\Item | \PHPUnit_Framework_MockObject_MockObject */
        $mockItem = $this->getMockBuilder('Magento\Tax\Service\V1\Data\QuoteDetails\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $mockItem->expects($this->once())
            ->method('getTaxIncluded')
            ->will($this->returnValue(false));

        $mockItem->expects($this->once())
            ->method('getDiscountAmount')
            ->will($this->returnValue(1));

        $this->mockConfig->expects($this->once())
            ->method('applyTaxAfterDiscount')
            ->will($this->returnValue(true));

        $addressRateRequest = new \Magento\Framework\Object();

        $this->mockCalculationTool->expects($this->once())
            ->method('getRateRequest')
            ->withAnyParameters()
            ->will($this->returnValue($addressRateRequest));

        $mockAppliedTaxRateBuilder = $this->getMockBuilder('Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('getAppliedTaxBuilder')
            ->will($this->returnValue($mockAppliedTaxRateBuilder));

        $this->mockCalculationTool->expects($this->once())
            ->method('getAppliedRates')
            ->withAnyParameters()
            ->will($this->returnValue([['id' => 0, 'percent' => 0, 'rates' => []]]));
        $expectedReturnValue = 'SOME RETURN OBJECT';
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('create')
            ->will($this->returnValue($expectedReturnValue));

        $this->assertSame($expectedReturnValue, $this->model->calculate($mockItem, 1));
    }
}
