<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Calculation;

class UnitBasedCalculatorTest extends \PHPUnit_Framework_TestCase
{
    const STORE_ID = 2300;
    const QUANTITY = 1;
    const UNIT_PRICE = 500;
    const RATE = 10;
    const STORE_RATE = 11;

    const CODE = 'CODE';
    const TYPE = 'TYPE';
    const ROW_TAX = 44.954135954136;

    /** @var \Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockTaxItemDetailsBuilder;

    /** @var \Magento\Tax\Model\Calculation | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockCalculationTool;

    /** @var \Magento\Tax\Model\Config | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockConfig;

    /** @var UnitBasedCalculator */
    protected $model;

    protected $addressRateRequest;

    public function setUp()
    {
        $this->mockTaxItemDetailsBuilder = $this->getMockBuilder('\Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockCalculationTool = $this->getMockBuilder('\Magento\Tax\Model\Calculation')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'round', 'getRate', 'getStoreRate', 'getRateRequest', 'getAppliedRates'])
            ->getMock();
        $this->mockConfig = $this->getMockBuilder('\Magento\Tax\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->addressRateRequest = new \Magento\Framework\Object();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $arguments = [
            'taxDetailsItemBuilder' => $this->mockTaxItemDetailsBuilder,
            'calculationTool'       => $this->mockCalculationTool,
            'config'                => $this->mockConfig,
            'storeId'               => self::STORE_ID,
            'addressRateRequest'    => $this->addressRateRequest
        ];
        $this->model = $objectManager->getObject('Magento\Tax\Model\Calculation\UnitBasedCalculator', $arguments);
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
        $mockItem->expects($this->once())
            ->method('getDiscountAmount')
            ->will($this->returnValue(1));
        $mockItem->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue(self::CODE));
        $mockItem->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(self::TYPE));
        $mockItem->expects($this->once())
            ->method('getUnitPrice')
            ->will($this->returnValue(self::UNIT_PRICE));

        $this->mockConfig->expects($this->once())
            ->method('crossBorderTradeEnabled')
            ->will($this->returnValue(false));
        $this->mockConfig->expects($this->once())
            ->method('applyTaxAfterDiscount')
            ->will($this->returnValue(true));

        $this->mockCalculationTool->expects($this->any())
            ->method('round')
            ->withAnyParameters()
            ->will($this->returnArgument(0));
        $this->mockCalculationTool->expects($this->once())
            ->method('getRate')
            ->with($this->addressRateRequest)
            ->will($this->returnValue(self::RATE));
        $this->mockCalculationTool->expects($this->once())
            ->method('getStoreRate')
            ->with($this->addressRateRequest, self::STORE_ID)
            ->will($this->returnValue(self::STORE_RATE));
        $this->mockCalculationTool->expects($this->once())
            ->method('getAppliedRates')
            ->withAnyParameters()
            ->will($this->returnValue([]));

        $mockAppliedTaxRateBuilder = $this->getMockBuilder('Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('getAppliedTaxBuilder')
            ->will($this->returnValue($mockAppliedTaxRateBuilder));
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('setCode')
            ->with(self::CODE);
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('setType')
            ->with(self::TYPE);
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('setRowTax')
            ->with(44.954135954136);
        $expectedReturnValue = 'SOME RETURN OBJECT';
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('create')
            ->will($this->returnValue($expectedReturnValue));

        $this->assertSame($expectedReturnValue, $this->model->calculate($mockItem, self::QUANTITY));
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
        $mockItem->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue(self::CODE));
        $mockItem->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(self::TYPE));
        $mockItem->expects($this->once())
            ->method('getUnitPrice')
            ->will($this->returnValue(self::UNIT_PRICE));

        $this->mockConfig->expects($this->once())
            ->method('applyTaxAfterDiscount')
            ->will($this->returnValue(true));

        $this->mockCalculationTool->expects($this->any())
            ->method('round')
            ->withAnyParameters()
            ->will($this->returnArgument(0));
        $this->mockCalculationTool->expects($this->once())
            ->method('getRate')
            ->with($this->addressRateRequest)
            ->will($this->returnValue(self::RATE));
        $this->mockCalculationTool->expects($this->once())
            ->method('getAppliedRates')
            ->withAnyParameters()
            ->will($this->returnValue([['id' => 0, 'percent' => 0, 'rates' => []]]));

        $mockAppliedTaxRateBuilder = $this->getMockBuilder('Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('getAppliedTaxBuilder')
            ->will($this->returnValue($mockAppliedTaxRateBuilder));
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('setCode')
            ->with(self::CODE);
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('setType')
            ->with(self::TYPE);
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('setRowTax')
            ->with(0.0);
        $expectedReturnValue = 'SOME RETURN OBJECT';
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('create')
            ->will($this->returnValue($expectedReturnValue));

        $this->assertSame($expectedReturnValue, $this->model->calculate($mockItem, self::QUANTITY));
    }
}
