<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Pricing\Price;

/**
 * Final Price test
 */
class FinalPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Pricing\Price\FinalPrice
     */
    protected $model;

    /**
     * @var \Magento\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\Catalog\Pricing\Price\BasePrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $basePriceMock;

    /**
     * @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableMock;

    /**
     * @var \Magento\Pricing\Adjustment\Calculator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $calculatorMock;

    /**
     * Set up function
     */
    public function setUp()
    {
        $this->saleableMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->priceInfoMock = $this->basePriceMock = $this->getMock(
            'Magento\Pricing\PriceInfo\Base',
            [],
            [],
            '',
            false
        );
        $this->basePriceMock = $this->getMock(
            'Magento\Catalog\Pricing\Price\BasePrice',
            [],
            [],
            '',
            false
        );

        $this->calculatorMock = $this->getMock(
            'Magento\Pricing\Adjustment\Calculator',
            [],
            [],
            '',
            false
        );

        $this->saleableMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));
        $this->priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo(\Magento\Catalog\Pricing\Price\BasePrice::PRICE_CODE))
            ->will($this->returnValue($this->basePriceMock));
        $this->model = new \Magento\Catalog\Pricing\Price\FinalPrice($this->saleableMock, 1, $this->calculatorMock);
    }

    /**
     * test for getValue
     */
    public function testGetValue()
    {
        $price = 10;
        $this->basePriceMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($price));
        $result = $this->model->getValue();
        $this->assertEquals($price, $result);
    }

    /**
     * Test getMinimalPrice()
     */
    public function testGetMinimalPrice()
    {
        $basePrice = 10;
        $minimalPrice = 5;
        $this->basePriceMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($basePrice));
        $this->calculatorMock->expects($this->once())
            ->method('getAmount')
            ->with($this->equalTo($basePrice))
            ->will($this->returnValue($minimalPrice));
        $this->saleableMock->expects($this->once())
            ->method('getMinimalPrice')
            ->will($this->returnValue(null));
        $result = $this->model->getMinimalPrice();
        $this->assertEquals($minimalPrice, $result);
    }

    /**
     * Test getMaximalPrice()
     */
    public function testGetMaximalPrice()
    {
        $basePrice = 10;
        $minimalPrice = 5;
        $this->basePriceMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($basePrice));
        $this->calculatorMock->expects($this->once())
            ->method('getAmount')
            ->with($this->equalTo($basePrice))
            ->will($this->returnValue($minimalPrice));
        $result = $this->model->getMaximalPrice();
        $this->assertEquals($minimalPrice, $result);
    }
}
