<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Helper\Product;

/**
 * Test class for Magento\Catalog\Helper\Product\Price
 */
class PriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Helper\Product\Price
     */
    protected $_helper;

    /**
     * @var \Magento\Tax\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxDataMock;

    /**
     * @var \Magento\Tax\Model\Calculation|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxCalculationMock;

    protected function setUp()
    {
        $this->taxDataMock = $this->getMock('Magento\Tax\Helper\Data', [], [], '', false, false);
        $this->taxCalculationMock = $this->getMock('Magento\Tax\Model\Calculation', [], [], '', false, false);

        $this->_helper = new \Magento\Catalog\Helper\Product\Price($this->taxDataMock, $this->taxCalculationMock);
    }

    public function testDisplayPriceExcludingTax()
    {
        $expectedValue = true;
        $this->taxDataMock->expects($this->once())
            ->method('displayPriceExcludingTax')
            ->will($this->returnValue(true));

        $result = $this->_helper->displayPriceExcludingTax();
        $this->assertEquals($expectedValue, $result);

    }
} 