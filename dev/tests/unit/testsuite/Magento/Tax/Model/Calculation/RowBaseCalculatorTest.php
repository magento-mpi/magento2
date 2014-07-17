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
use Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder;

/**
 * Class RowBaseCalculatorTest
 *
 */
class RowBaseCalculatorTest extends \PHPUnit_Framework_TestCase
{
    const STORE_ID = 2300;

    /** @var objectManager */
    protected $objectManager;

    /** @var \Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockTaxItemDetailsBuilder;

    /** @var \Magento\Tax\Model\Calculation | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockCalculationTool;

    /** @var \Magento\Tax\Model\Config | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockConfig;

    /** @var $mockItem \Magento\Tax\Service\V1\Data\QuoteDetails\Item | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockItem;

    /** @var $mockAppliedTaxBuilder AppliedTaxBuilder | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockAppliedTaxBuilder;

    /** @var $mockAppliedTaxRateBuilder AppliedTaxBuilder | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockAppliedTaxRateBuilder;

    /** @var \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTax | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockAppliedTax;

    protected $addressRateRequest;

    /** @var RowBaseAndTotalBaseCalculatorTestHelper */
    protected $rowBaseAndTotalBaseCalculatorHelper;


    /** @var RowBaseCalculator */
    protected $rowBaseCalculator;


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

        $this->mockItem = $this->getMockBuilder('Magento\Tax\Service\V1\Data\QuoteDetails\Item')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockAppliedTaxRateBuilder = $this->getMockBuilder(
            'Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxRateBuilder'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockAppliedTaxBuilder = $this->getMockBuilder(
            'Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockAppliedTax = $this->getMockBuilder(
            'Magento\Tax\Service\V1\Data\TaxDetails\AppliedTax'
        )->disableOriginalConstructor()
            ->getMock();

        $this->mockAppliedTax->expects($this->any())->method('getTaxRateKey')->will($this->returnValue('taxKey'));
        $this->rowBaseCalculator = $this->objectManager->getObject(
            'Magento\Tax\Model\Calculation\RowBaseCalculator',
            [
                'taxDetailsItemBuilder' => $this->mockTaxItemDetailsBuilder,
                'calculationTool' => $this->mockCalculationTool,
                'config' => $this->mockConfig,
                'storeId' => self::STORE_ID,
                'addressRateRequest' => $this->addressRateRequest
            ]
        );
        $this->rowBaseAndTotalBaseCalculatorHelper = new RowBaseAndTotalBaseCalculatorTestHelper(
            $this->objectManager,
            $this->mockTaxItemDetailsBuilder,
            $this->mockCalculationTool,
            $this->mockConfig,
            $this->mockItem,
            $this->mockAppliedTaxBuilder,
            $this->mockAppliedTaxRateBuilder,
            $this->mockAppliedTax
        );
    }

    public function testCalculateWithTaxInPrice()
    {
        $this->rowBaseAndTotalBaseCalculatorHelper->getMocks(true);

        $expectedReturnValue = 'SOME RETURN OBJECT';
        $this->assertSame(
            $expectedReturnValue,
            $this->rowBaseAndTotalBaseCalculatorHelper->calculate($this->rowBaseCalculator)
        );
    }

    public function testCalculateWithTaxNotInPrice()
    {
        $this->rowBaseAndTotalBaseCalculatorHelper->getMocks(false);
        $expectedReturnValue = 'SOME RETURN OBJECT';

        $this->assertSame(
            $expectedReturnValue,
            $this->rowBaseAndTotalBaseCalculatorHelper->calculate($this->rowBaseCalculator)
        );
    }
}
