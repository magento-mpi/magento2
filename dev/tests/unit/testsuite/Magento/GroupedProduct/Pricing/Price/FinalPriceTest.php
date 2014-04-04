<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Pricing\Price;

/**
 * Class FinalPriceTest
 */
class FinalPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GroupedProduct\Pricing\Price\FinalPrice
     */
    protected $finalPrice;

    /**
     * @var \Magento\GroupedProduct\Model\Product\Type\Grouped|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeInstanceMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $salableItemMock;

    /**
     * @var \Magento\Pricing\Adjustment\Calculator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $calculatorMock;

    /**
     * @var \Magento\Pricing\PriceInfo\Base|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \Magento\Pricing\Amount\AmountInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $amountMock;

    /**
     * @var \Magento\Catalog\Pricing\Price\FinalPrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceTypeMock;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->salableItemMock =  $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->amountMock = $this->getMock('Magento\Pricing\Amount\Base', [], [], '', false);
        $this->calculatorMock = $this->getMock('Magento\Pricing\Adjustment\Calculator', [], [], '', false);
        $this->priceInfoMock = $this->getMock('Magento\Pricing\PriceInfo\Base', [], [], '', false);
        $this->typeInstanceMock = $this->getMock('Magento\GroupedProduct\Model\Product\Type\Grouped',
            [], [], '', false);
        $this->priceTypeMock = $this->getMock('Magento\Catalog\Pricing\Price\FinalPrice', [], [], '', false);

        $this->finalPrice = new \Magento\GroupedProduct\Pricing\Price\FinalPrice
        (
            $this->salableItemMock,
            $this->calculatorMock
        );
    }

    public function testGetMinProduct()
    {
        $valueMap = [
            [90],
            [70]
        ];
        $this->salableItemMock->expects($this->once())
            ->method('getTypeInstance')
            ->will($this->returnValue($this->typeInstanceMock));

        $this->typeInstanceMock->expects($this->once())
            ->method('getAssociatedProducts')
            ->with($this->equalTo($this->salableItemMock))
            ->will($this->returnValue([$this->productMock, $this->productMock]));

        $this->productMock->expects($this->exactly(2))
            ->method('setQty')
            ->with($this->equalTo(\Magento\Pricing\PriceInfoInterface::PRODUCT_QUANTITY_DEFAULT));

        $this->productMock->expects($this->exactly(2))
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));

        $this->priceInfoMock->expects($this->exactly(2))
            ->method('getPrice')
            ->with($this->equalTo(\Magento\Catalog\Pricing\Price\FinalPriceInterface::PRICE_TYPE_FINAL))
            ->will($this->returnValue($this->priceTypeMock));

        $this->priceTypeMock->expects($this->exactly(2))
            ->method('getValue')
            ->will($this->returnValueMap($valueMap));
        $this->assertEquals($this->finalPrice->getMinProduct(), $this->productMock);
    }
}
