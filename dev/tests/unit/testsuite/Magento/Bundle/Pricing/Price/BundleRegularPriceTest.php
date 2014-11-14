<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class BundleRegularPriceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Bundle\Pricing\Price\BundleRegularPrice */
    protected $regularPrice;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $saleableInterfaceMock;

    /** @var \Magento\Bundle\Pricing\Adjustment\BundleCalculatorInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $bundleCalculatorMock;

    /** @var \Magento\Framework\Pricing\PriceInfo\Base |\PHPUnit_Framework_MockObject_MockObject */
    protected $priceInfoMock;

    /**
     * @var int
     */
    protected $quantity = 1;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->saleableInterfaceMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->bundleCalculatorMock = $this->getMock('Magento\Bundle\Pricing\Adjustment\BundleCalculatorInterface');

        $this->priceInfoMock = $this->getMock('Magento\Framework\Pricing\PriceInfo\Base', [], [], '', false);

        $this->saleableInterfaceMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->regularPrice = new \Magento\Bundle\Pricing\Price\BundleRegularPrice(
            $this->saleableInterfaceMock,
            $this->quantity,
            $this->bundleCalculatorMock
        );
    }

    public function testGetAmount()
    {
        $expectedResult = 5;

        $this->saleableInterfaceMock->expects($this->once())
            ->method('getPrice')
            ->will($this->returnValue($expectedResult));

        $this->bundleCalculatorMock->expects($this->once())
            ->method('getMinRegularAmount')
            ->with($expectedResult, $this->saleableInterfaceMock)
            ->will($this->returnValue($expectedResult));

        $result = $this->regularPrice->getAmount();
        $this->assertEquals($expectedResult, $result, 'Incorrect amount');

        //Calling a second time, should use cached value
        $result = $this->regularPrice->getAmount();
        $this->assertEquals($expectedResult, $result, 'Incorrect amount');
    }

    public function testGetMaximalPrice()
    {
        $expectedResult = 5;

        $this->saleableInterfaceMock->expects($this->once())
            ->method('getPrice')
            ->will($this->returnValue($expectedResult));

        $this->bundleCalculatorMock->expects($this->once())
            ->method('getMaxRegularAmount')
            ->with($expectedResult, $this->saleableInterfaceMock)
            ->will($this->returnValue($expectedResult));

        $result = $this->regularPrice->getMaximalPrice();
        $this->assertEquals($expectedResult, $result, 'Incorrect amount');

        //Calling a second time, should use cached value
        $result = $this->regularPrice->getMaximalPrice();
        $this->assertEquals($expectedResult, $result, 'Incorrect amount the second time');
    }

    public function testGetMinimalPrice()
    {
        $expectedResult = 5;

        $this->saleableInterfaceMock->expects($this->once())
            ->method('getPrice')
            ->will($this->returnValue($expectedResult));

        $this->bundleCalculatorMock->expects($this->once())
            ->method('getMinRegularAmount')
            ->with($expectedResult, $this->saleableInterfaceMock)
            ->will($this->returnValue($expectedResult));

        $result = $this->regularPrice->getMinimalPrice();
        $this->assertEquals($expectedResult, $result, 'Incorrect amount');

        //Calling a second time, should use cached value
        $result = $this->regularPrice->getMinimalPrice();
        $this->assertEquals($expectedResult, $result, 'Incorrect amount the second time');
    }
}
