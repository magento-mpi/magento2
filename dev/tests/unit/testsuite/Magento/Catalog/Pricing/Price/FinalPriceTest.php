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
    /** @var \Magento\Catalog\Pricing\Price\FinalPrice  */
    protected $model;

    /** @var \Magento\Pricing\PriceInfoInterface  */
    protected $priceInfoMock;

    /** @var \Magento\Catalog\Pricing\Price\BasePrice */
    protected $basePriceMock;

    /**
     * Set up function
     */
    public function setUp()
    {
        $saleableMock = $this->getMockForAbstractClass('Magento\Pricing\Object\SaleableInterface');
        $this->priceInfoMock = $this->getMockForAbstractClass('Magento\Pricing\PriceInfoInterface');
        $this->basePriceMock = $this->getMock(
            'Magento\Catalog\Pricing\Price\BasePrice',
            ['getDisplayValue', 'getMaxValue'],
            [],
            '',
            false
        );

        $saleableMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));
        $this->priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with('base_price')
            ->will($this->returnValue($this->basePriceMock));
        $this->model = new \Magento\Catalog\Pricing\Price\FinalPrice($saleableMock);
    }

    /**
     * test for getMaxValue
     */
    public function testGetMaxValue()
    {
        $this->basePriceMock->expects($this->exactly(3))
            ->method('getDisplayValue')
            ->will($this->returnArgument(0));
        $this->basePriceMock->expects($this->exactly(3))
            ->method('getMaxValue')
            ->will($this->returnValue(10));
        $this->assertEquals(10, $this->model->getMaxValue());
        $this->assertEquals($this->model->getMaxValue(), $this->model->getMaximumPrice());
    }

    /**
     * test for getValue
     */
    public function testGetValue()
    {
        $this->basePriceMock->expects($this->exactly(3))
            ->method('getDisplayValue')
            ->will($this->returnValue(10));
        $this->assertEquals(10, $this->model->getValue());
        $this->assertEquals($this->model->getValue(), $this->model->getMinimalPrice());
    }


    /**
     * test for getDisplayValue
     */
    public function testGetDisplayValue()
    {
        $expected = 'based on "10" and "code" args';
        $this->basePriceMock->expects($this->once())
            ->method('getDisplayValue')
            ->with(10, 'code')
            ->will($this->returnValue($expected));
        $this->assertEquals($expected, $this->model->getDisplayValue(10, 'code'));
    }
}
