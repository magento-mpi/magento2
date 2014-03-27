<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

/**
 * Class RegularPriceTest
 */
class RegularPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Pricing\Price\RegularPrice
     */
    protected $regularPrice;

    /**
     * @var \Magento\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\Pricing\Amount|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $amountMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $salableItemMock;

    /**
     * Test setUp
     */
    protected function setUp()
    {
        $this->salableItemMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->priceInfoMock = $this->getMock('Magento\Pricing\PriceInfo\Base', [], [], '', false);
        $this->amountMock = $this->getMock('Magento\Pricing\Amount', [], [], '', false);
        $this->salableItemMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));
        $this->regularPrice = new RegularPrice($this->salableItemMock, 1);
    }

    /**
     * Test method testGetValue
     */
    public function testGetValue()
    {
        $this->salableItemMock->expects($this->once())
            ->method('getPrice')
            ->will($this->returnValue(100));
        $this->assertEquals(100, $this->regularPrice->getValue());
    }

    /**
     * Test method testGetDisplayValue
     */
    public function testGetDisplayValue()
    {
        $this->salableItemMock->expects($this->once())
            ->method('getPrice')
            ->will($this->returnValue(77));
        $this->priceInfoMock->expects($this->once())
            ->method('getAmount')
            ->with($this->equalTo(77))
            ->will($this->returnValue($this->amountMock));
        $this->amountMock->expects($this->once())
            ->method('getDisplayAmount')
            ->with($this->equalTo('excluded-code'))
            ->will($this->returnValue(77));
        $this->assertEquals(77, $this->regularPrice->getDisplayValue(null, 'excluded-code'));
    }
}
