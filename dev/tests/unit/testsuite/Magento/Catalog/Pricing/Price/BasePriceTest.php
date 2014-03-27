<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Pricing\Price;

/**
 * Base price test
 */
class BasePriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Pricing\Price\BasePrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $basePrice;

    /**
     * @var \Magento\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $salableItemMock;

    /**
     * @var \Magento\Catalog\Pricing\Price\RegularPrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $regularPriceMock;

    /**
     * @var \Magento\Catalog\Pricing\Price\GroupPrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupPriceMock;

    /**
     * @var \Magento\Catalog\Pricing\Price\SpecialPrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $specialPriceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $prices;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->salableItemMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->priceInfoMock = $this->getMock('Magento\Pricing\PriceInfo\Base', [], [], '', false);
        $this->regularPriceMock = $this->getMock('Magento\Catalog\Pricing\Price\RegularPrice', [], [], '', false);
        $this->groupPriceMock = $this->getMock('Magento\Catalog\Pricing\Price\GroupPrice', [], [], '', false);
        $this->specialPriceMock= $this->getMock('Magento\Catalog\Pricing\Price\SpecialPrice', [], [], '', false);

        $this->priceInfoMock->expects($this->once())
            ->method('getAdjustments')
            ->will($this->returnValue([]));

        $this->salableItemMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));

        $this->prices = [
            'regular_price' => $this->regularPriceMock,
            'group_price' => $this->groupPriceMock,
            'special_price' => $this->specialPriceMock
        ];
        $this->priceInfoMock->expects($this->exactly(2))
            ->method('getPricesIncludedInBase')
            ->will($this->returnValue($this->prices));

        $this->regularPriceMock->expects($this->exactly(4))
            ->method('getValue')
            ->will($this->returnValue(100));

        $this->groupPriceMock->expects($this->exactly(2))
            ->method('getValue')
            ->will($this->returnValue(99));

        $this->specialPriceMock->expects($this->exactly(2))
            ->method('getValue')
            ->will($this->returnValue(77));

        $this->basePrice = new BasePrice($this->salableItemMock, 1);
    }

    /**
     * Test method getValue
     */
    public function testGetMinMaxValue()
    {
        $this->assertSame(77, $this->basePrice->getValue());
        $this->assertSame(100, $this->basePrice->getMaxValue());

        // test false value if price was not set
        $this->salableItemMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->priceInfoMock = $this->getMock('Magento\Pricing\PriceInfo\Base', [], [], '', false);

        $this->priceInfoMock->expects($this->once())
            ->method('getAdjustments')
            ->will($this->returnValue([]));

        $this->salableItemMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));

        $this->priceInfoMock->expects($this->once())
            ->method('getPricesIncludedInBase')
            ->will($this->returnValue([]));

        $this->basePrice = new BasePrice($this->salableItemMock, 1);
        $this->assertSame(false, $this->basePrice->getValue());
    }
}
