<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Calculation;

/**
 * Class RowBaseCalculatorTest
 *
 */
class RowBaseCalculatorTest extends RowBaseAndTotalBaseCalculatorTestCase
{

    /** @var RowBaseCalculator | \PHPUnit_Framework_MockObject_MockObject */
    protected $rowBaseCalculator;

    public function testCalculateWithTaxInPrice()
    {
        $this->initMocks(true);
        $this->initRowBaseCalculator();
        $this->rowBaseCalculator->expects($this->once())
            ->method('deltaRound')->will($this->returnValue(0));

        $this->assertSame(
            self::EXPECTED_VALUE,
            $this->calculate($this->rowBaseCalculator)
        );
    }

    public function testCalculateWithTaxNotInPrice()
    {
        $this->initMocks(false);
        $this->initRowBaseCalculator();
        $this->rowBaseCalculator->expects($this->never())
            ->method('deltaRound');

        $this->assertSame(
            self::EXPECTED_VALUE,
            $this->calculate($this->rowBaseCalculator)
        );
    }

    private function initRowBaseCalculator()
    {
        $taxClassService = $this->objectManager->getObject('Magento\Tax\Service\V1\TaxClassService');
        $this->rowBaseCalculator = $this->getMockBuilder('Magento\Tax\Model\Calculation\RowBaseCalculator')
            ->setConstructorArgs(
                [
                    'taxClassService' => $taxClassService,
                    'taxDetailsItemBuilder' => $this->mockTaxItemDetailsBuilder,
                    'calculationTool' => $this->mockCalculationTool,
                    'config' => $this->mockConfig,
                    'storeId' => self::STORE_ID,
                    'addressRateRequest' => $this->addressRateRequest
                ]
            )->setMethods(['deltaRound'])->getMock();
    }
}
