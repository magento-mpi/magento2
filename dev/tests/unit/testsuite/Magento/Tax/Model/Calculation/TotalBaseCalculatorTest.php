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
 * Class TotalBaseCalculatorTest
 *
 */
class TotalBaseCalculatorTest extends RowBaseAndTotalBaseCalculatorTestCase
{
    /** @var TotalBaseCalculator | \PHPUnit_Framework_MockObject_MockObject */
    protected $totalBaseCalculator;

    public function testCalculateWithTaxInPrice()
    {
        $this->initTotalBaseCalculator();
        $this->totalBaseCalculator->expects($this->exactly(3))
            ->method('deltaRound')->will($this->returnValue(0));
        $this->initMocks(true);

        $this->assertSame(
            self::EXPECTED_VALUE,
            $this->calculate($this->totalBaseCalculator)
        );
    }

    public function testCalculateWithTaxNotInPrice()
    {
        $this->initTotalBaseCalculator();
        $this->totalBaseCalculator->expects($this->exactly(2))
            ->method('deltaRound')->will($this->returnValue(0));
        $this->initMocks(false);

        $this->assertSame(
            self::EXPECTED_VALUE,
            $this->calculate($this->totalBaseCalculator)
        );
    }

    private function initTotalBaseCalculator()
    {
        $taxClassService = $this->objectManager->getObject('Magento\Tax\Service\V1\TaxClassService');
        $this->totalBaseCalculator = $this->getMockBuilder('Magento\Tax\Model\Calculation\TotalBaseCalculator')
            ->setConstructorArgs(
                [
                    'taxClassService' => $taxClassService,
                    'taxDetailsItemBuilder' => $this->taxItemDetailsBuilder,
                    'calculationTool' => $this->mockCalculationTool,
                    'config' => $this->mockConfig,
                    'storeId' => self::STORE_ID,
                    'addressRateRequest' => $this->addressRateRequest
                ]
            )->setMethods(['deltaRound'])->getMock();
    }
}
