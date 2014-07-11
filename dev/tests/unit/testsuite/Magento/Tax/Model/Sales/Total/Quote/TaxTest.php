<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Tax\Model\Sales\Total\Quote\Tax
 */
namespace Magento\Tax\Model\Sales\Total\Quote;

use Magento\Tax\Model\Calculation;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the specific method
     *
     * @param string $calculationSequence
     * @param string $keyExpected
     * @param string $keyAbsent
     * @dataProvider dataProviderProcessConfigArray
     */
    public function testProcessConfigArray($calculationSequence, $keyExpected, $keyAbsent)
    {
        $taxData = $this->getMock('Magento\Tax\Helper\Data', [], [], '', false);
        $taxData
            ->expects($this->any())
            ->method('getCalculationSequence')
            ->will($this->returnValue($calculationSequence));

        $config = $this->getMock('\Magento\Tax\Model\Config', [], [], '', false);
        $taxCalculationService = $this->getMock('\Magento\Tax\Service\V1\TaxCalculationService', [], [], '', false);
        $quoteDetailsBuilder = $this->getMock('\Magento\Tax\Service\V1\Data\QuoteDetailsBuilder', [], [], '', false);

        /** @var \Magento\Tax\Model\Sales\Total\Quote\Tax */
        $taxTotalsCalcModel = new Tax($taxData, $config, $taxCalculationService, $quoteDetailsBuilder);
        $array = $taxTotalsCalcModel->processConfigArray([], null);
        $this->assertArrayHasKey($keyExpected, $array, 'Did not find the expected array key: ' . $keyExpected);
        $this->assertArrayNotHasKey($keyAbsent, $array, 'Should not have found the array key; ' . $keyAbsent);
    }

    /**
     * @return array
     */
    public function dataProviderProcessConfigArray()
    {
        return [
            [Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL, 'before', 'after'],
            [Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL, 'after', 'before'],
            [Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL, 'after', 'before'],
            [Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL, 'after', 'before']
        ];
    }
}
