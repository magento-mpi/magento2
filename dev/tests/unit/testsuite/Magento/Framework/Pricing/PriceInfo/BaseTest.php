<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Pricing\PriceInfo;

/**
 * Test class for \Magento\Framework\Pricing\PriceInfo\Base
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Pricing\Object\SaleableInterface
     */
    protected $saleableItem;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Pricing\PriceComposite
     */
    protected $prices;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Pricing\Adjustment\Collection
     */
    protected $adjustmentCollection;

    /**
     * @var float
     */
    protected $quantity;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Pricing\Amount\AmountFactory
     */
    protected $amountFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Base
     */
    protected $model;

    public function setUp()
    {
        $this->saleableItem = $this->getMock('Magento\Framework\Pricing\Object\SaleableInterface', [], [], '', false);
        $this->prices = $this->getMock('Magento\Framework\Pricing\PriceComposite', [], [], '', false);
        $this->adjustmentCollection = $this->getMock('Magento\Framework\Pricing\Adjustment\Collection', [], [], '', false);
        $this->amountFactory = $this->getMock('Magento\Framework\Pricing\Amount\AmountFactory', [], [], '', false);
        $this->quantity = 3.;
        $this->model = new Base(
            $this->saleableItem,
            $this->prices,
            $this->adjustmentCollection,
            $this->amountFactory,
            $this->quantity
        );
    }

    /**
     * @covers \Magento\Framework\Pricing\PriceInfo\Base::__construct
     * @covers \Magento\Framework\Pricing\PriceInfo\Base::initPrices
     * @covers \Magento\Framework\Pricing\PriceInfo\Base::getPrices
     */
    public function testGetPrices()
    {
        $this->prices->expects($this->once())
            ->method('getPriceCodes')
            ->will($this->returnValue(['test1', 'test2']));
        $this->prices->expects($this->at(1))->method('createPriceObject')
            ->with($this->saleableItem, 'test1', $this->quantity)->will($this->returnValue('1'));
        $this->prices->expects($this->at(2))->method('createPriceObject')
            ->with($this->saleableItem, 'test2', $this->quantity)->will($this->returnValue('2'));
        $this->assertEquals(['test1' => '1', 'test2' => '2'], $this->model->getPrices());
    }

    /**
     * @covers \Magento\Framework\Pricing\PriceInfo\Base::__construct
     * @covers \Magento\Framework\Pricing\PriceInfo\Base::getPrice
     * @dataProvider providerGetPrice
     */
    public function testGetPrice($entryParams, $createCount)
    {
        list($priceCode, $quantity) = array_values(reset($entryParams));
        $this->prices->expects($this->exactly($createCount))->method('createPriceObject')
            ->with($this->saleableItem, $priceCode, $quantity ? : $this->quantity)->will(
                $this->returnValue('basePrice')
            );

        foreach ($entryParams as $params) {
            list($priceCode, $quantity) = array_values($params);
            $this->assertEquals('basePrice', $this->model->getPrice($priceCode, $quantity));
        }
    }

    /**
     * Data provider for testGetPrice
     *
     * @return array
     */
    public function providerGetPrice()
    {
        return [
            'case with empty quantity' => [
                'entryParams' => [
                    ['priceCode' => 'testCode', 'quantity' => null]
                ],
                'createCount' => 1
            ],
            'case with existing price' => [
                'entryParams' => [
                    ['priceCode' => 'testCode', 'quantity' => null],
                    ['priceCode' => 'testCode', 'quantity' => null]
                ],
                'createCount' => 1
            ],
            'case with quantity' => [
                'entryParams' => [
                    ['priceCode' => 'testCode', 'quantity' => 2.]
                ],
                'createCount' => 1
            ],
        ];
    }

    /**
     * @covers \Magento\Framework\Pricing\PriceInfo\Base::getAdjustments
     */
    public function testGetAdjustments()
    {
        $this->adjustmentCollection->expects($this->once())->method('getItems')->will($this->returnValue('result'));
        $this->assertEquals('result', $this->model->getAdjustments());
    }

    /**
     * @covers \Magento\Framework\Pricing\PriceInfo\Base::getAdjustment
     */
    public function testGetAdjustment()
    {
        $this->adjustmentCollection->expects($this->any())->method('getItemByCode')
            ->with('test1')
            ->will($this->returnValue('adjustment'));
        $this->assertEquals('adjustment', $this->model->getAdjustment('test1'));
    }

    /**
     * @covers \Magento\Framework\Pricing\PriceInfo\Base::getPricesIncludedInBase
     */
    public function testGetPricesIncludedInBase()
    {
        $this->prices->expects($this->once())
            ->method('getMetadata')
            ->will(
                $this->returnValue(
                    [
                        'test1' => ['class' => 'class1', 'include_in_base_price' => false],
                        'test2' => ['class' => 'class2', 'include_in_base_price' => true]
                    ]
                )
            );

        $priceModelMock = $this->getMock('Magento\Catalog\Pricing\Price\SpecialPrice', [], [], '', false);
        $priceModelMock->expects($this->once())->method('getValue')->will($this->returnValue(2.5));
        $this->prices->expects($this->at(1))->method('createPriceObject')
            ->with($this->saleableItem, 'test2', $this->quantity)->will($this->returnValue($priceModelMock));

        $this->assertSame([$priceModelMock], $this->model->getPricesIncludedInBase());
    }
}
