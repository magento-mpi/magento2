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
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $salableItemMock;

    protected function setUp()
    {
        $this->salableItemMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->priceInfoMock = $this->getMock('Magento\Pricing\PriceInfo\Base', [], [], '', false);

        $this->priceInfoMock->expects($this->once())
            ->method('getAdjustments')
            ->will($this->returnValue([]));

        $this->salableItemMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));

        $this->salableItemMock->expects($this->exactly(2))
            ->method('getPrice')
            ->will($this->returnValue(100));

        $this->regularPrice = new RegularPrice($this->salableItemMock, 1);
    }

    /**
     * test retrieving of value
     */
    public function testGetValue()
    {
        $this->assertEquals(100, $this->regularPrice->getValue());
    }
}
