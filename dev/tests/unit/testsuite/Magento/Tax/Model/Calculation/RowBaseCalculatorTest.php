<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Calculation;


use Magento\Tax\Model\Calculation;
use Magento\TestFramework\Helper\ObjectManager;
use Magento\Tax\Service\V1\Data\QuoteDetails;

/**
 * Class RowBaseCalculatorTest
 *
 */
class RowBaseCalculatorTest extends \PHPUnit_Framework_TestCase
{
    const STORE_ID = 2300;
    const QUANTITY = 1;
    const UNIT_PRICE = 500;
    const RATE = 10;
    const STORE_RATE = 11;

    const CODE = 'CODE';
    const TYPE = 'TYPE';
    const ROW_TAX = 44.954135954136;

    /** @var objectManager */
    protected $objectManager;

    /** @var \Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockTaxItemDetailsBuilder;

    /** @var \Magento\Tax\Model\Calculation | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockCalculationTool;

    /** @var \Magento\Tax\Model\Config | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockConfig;

    protected $addressRateRequest;

    /** @var RowBaseCalculator */
    protected $rowBasedCalculator;


    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->mockTaxItemDetailsBuilder = $this->getMockBuilder('\Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockCalculationTool = $this->getMockBuilder('\Magento\Tax\Model\Calculation')
            ->disableOriginalConstructor()
            ->setMethods(
                ['__wakeup', 'round', 'getRate', 'getStoreRate', 'getRateRequest', 'getAppliedRates', 'calcTaxAmount']
            )
            ->getMock();
        $this->mockConfig = $this->getMockBuilder('\Magento\Tax\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->addressRateRequest = new \Magento\Framework\Object();

        $this->rowBasedCalculator = $this->objectManager->getObject(
            'Magento\Tax\Model\Calculation\RowBaseCalculator',
            [
                'taxDetailsItemBuilder' => $this->mockTaxItemDetailsBuilder,
                'calculationTool' => $this->mockCalculationTool,
                'config' => $this->mockConfig,
                'storeId' => self::STORE_ID,
                'addressRateRequest' => $this->addressRateRequest
            ]
        );
    }

    public function testCalculateWithTaxInPrice()
    {
        $mockItem = $this->getMockItem();
        $mockItem->expects($this->once())
            ->method('getTaxIncluded')
            ->will($this->returnValue(true));

        $this->mockCalculationTool->expects($this->once())
            ->method('getRate')
            ->with($this->addressRateRequest)
            ->will($this->returnValue(1.2));

        $this->mockCalculationTool->expects($this->atLeastOnce())
            ->method('round')
            ->withAnyParameters()
            ->will($this->returnValue(1.1));

        $this->mockCalculationTool->expects($this->once())
            ->method('getStoreRate')
            ->withAnyParameters()
            ->will($this->returnValue(1.2));

        $this->mockCalculationTool->expects($this->atLeastOnce())
            ->method('calcTaxAmount')
            ->withAnyParameters()
            ->will($this->returnValue(1.1));

        $this->mockConfig->expects($this->once())
            ->method('applyTaxAfterDiscount')
            ->will($this->returnValue(true));

        $mockItem->expects($this->once())
            ->method('getDiscountAmount')
            ->will($this->returnValue(1));

        $this->mockCalculationTool->expects($this->once())
            ->method('getAppliedRates')
            ->with($this->addressRateRequest)
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
            ->with(1.1);
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('setTaxPercent')
            ->with(1.2);


        $expectedReturnValue = 'SOME RETURN OBJECT';
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('create')
            ->will($this->returnValue($expectedReturnValue));
        $this->assertSame($expectedReturnValue, $this->rowBasedCalculator->calculate($mockItem, 1));
    }

    public function testCalculateWithTaxNotInPrice()
    {
        $mockItem = $this->getMockItem();
        $mockItem->expects($this->once())
            ->method('getTaxIncluded')
            ->will($this->returnValue(false));

        $this->mockConfig->expects($this->once())
            ->method('applyTaxAfterDiscount')
            ->will($this->returnValue(true));

        $this->mockCalculationTool->expects($this->atLeastOnce())
            ->method('round')
            ->withAnyParameters()
            ->will($this->returnValue(1.3));


        $this->mockCalculationTool->expects($this->atLeastOnce())
            ->method('calcTaxAmount')
            ->withAnyParameters()
            ->will($this->returnValue(1.5));

        $this->mockConfig->expects($this->once())
            ->method('applyTaxAfterDiscount')
            ->will($this->returnValue(true));

        $mockItem->expects($this->once())
            ->method('getDiscountAmount')
            ->will($this->returnValue(1));

        $this->mockCalculationTool->expects($this->once())
            ->method('getAppliedRates')
            ->withAnyParameters()
            ->will($this->returnValue([['id' => 0, 'percent' => 0, 'rates' => []]]));
        $this->mockCalculationTool->expects($this->once())
            ->method('getRate')
            ->with($this->addressRateRequest)
            ->will($this->returnValue(self::RATE));

        $mockAppliedTaxRateBuilder = $this->getMockBuilder('Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('getAppliedTaxBuilder')
            ->will($this->returnValue($mockAppliedTaxRateBuilder));
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('setType')
            ->with(self::TYPE);
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('setCode')
            ->with(self::CODE);
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('setRowTax')
            ->with(1.3);
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('setTaxPercent')
            ->with(self::RATE);
        $expectedReturnValue = 'SOME RETURN OBJECT';
        $this->mockTaxItemDetailsBuilder->expects($this->once())
            ->method('create')
            ->will($this->returnValue($expectedReturnValue));

        $this->assertSame($expectedReturnValue, $this->rowBasedCalculator->calculate($mockItem, 1));
    }

    /**
     * @return \Magento\Tax\Service\V1\Data\QuoteDetails\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockItem()
    {
        /** @var $mockItem \Magento\Tax\Service\V1\Data\QuoteDetails\Item | \PHPUnit_Framework_MockObject_MockObject */
        $mockItem = $this->getMockBuilder('Magento\Tax\Service\V1\Data\QuoteDetails\Item')
            ->disableOriginalConstructor()
            ->getMock();
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

        return $mockItem;
    }
}
