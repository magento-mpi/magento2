<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\PriceInfo;

/**
 * Test class for \Magento\Pricing\PriceInfo\Base
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Pricing\Object\SaleableInterface
     */
    protected $product;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Pricing\PriceComposite
     */
    protected $prices;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Pricing\AdjustmentComposite
     */
    protected $adjustments;

    /**
     * @var float
     */
    protected $quantity;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Pricing\Amount\AmountFactory
     */
    protected $amountFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Base
     */
    protected $model;

    public function setUp()
    {
        $this->product = $this->getMock('Magento\Pricing\Object\SaleableInterface', [], [], '', false);
        $this->prices = $this->getMock('Magento\Pricing\PriceComposite', [], [], '', false);
        $this->adjustments = $this->getMock('Magento\Pricing\AdjustmentComposite', [], [], '', false);
        $this->amountFactory = $this->getMock('Magento\Pricing\Amount\AmountFactory', [], [], '', false);
        $this->quantity = 3.;
        $this->model = new Base(
            $this->product,
            $this->prices,
            $this->adjustments,
            $this->amountFactory,
            $this->quantity
        );
    }

    /**
     * @covers \Magento\Pricing\PriceInfo\Base::__construct
     * @covers \Magento\Pricing\PriceInfo\Base::initPrices
     * @covers \Magento\Pricing\PriceInfo\Base::getPrices
     */
    public function testGetPrices()
    {
        $this->prices->expects($this->once())
            ->method('getPriceCodes')
            ->will($this->returnValue(['test1', 'test2']));
        $this->prices->expects($this->at(1))->method('createPriceObject')
            ->with($this->product, 'test1', $this->quantity)->will($this->returnValue('1'));
        $this->prices->expects($this->at(2))->method('createPriceObject')
            ->with($this->product, 'test2', $this->quantity)->will($this->returnValue('2'));
        $this->assertEquals(['test1' => '1', 'test2' => '2'], $this->model->getPrices());
    }

    /**
     * @covers \Magento\Pricing\PriceInfo\Base::__construct
     * @covers \Magento\Pricing\PriceInfo\Base::getPrice
     * @dataProvider providerGetPrice
     */
    public function testGetPrice($entryParams, $createCount)
    {
        list($priceCode, $quantity) = array_values(reset($entryParams));
        $this->prices->expects($this->exactly($createCount))->method('createPriceObject')
            ->with($this->product, $priceCode, $quantity ?: $this->quantity)-> will($this->returnValue('basePrice'));

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
     * @covers \Magento\Pricing\PriceInfo\Base::getAdjustments
     */
    public function testGetAdjustments()
    {
        $this->adjustments->expects($this->once())->method('getAdjustments')->will($this->returnValue('result'));
        $this->assertEquals('result', $this->model->getAdjustments());
    }

    /**
     * @covers \Magento\Pricing\PriceInfo\Base::getAdjustment
     * @expectedException \InvalidArgumentException
     */
    public function testGetAdjustment()
    {
        $this->adjustments->expects($this->any())->method('getAdjustments')
            ->will($this->returnValue(['test1' => 'adjustment']));
        $this->assertEquals('adjustment', $this->model->getAdjustment('test1'));

        $this->model->getAdjustment('not_existed');
    }

    /**
     * @covers \Magento\Pricing\PriceInfo\Base::getPricesIncludedInBase
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
            ->with($this->product, 'test2', $this->quantity)->will($this->returnValue($priceModelMock));

        $this->assertSame([$priceModelMock], $this->model->getPricesIncludedInBase());
    }

    public function testGetAmount()
    {
        $amount = 2.;
        $result = $this->getMock('Magento\Pricing\Amount', [], [], '', false);
        $this->amountFactory->expects($this->once())
            ->method('create')
            ->with($this->equalTo($this->adjustments), $this->equalTo($this->product), $this->equalTo($amount))
            ->will($this->returnValue($result));
        $this->assertSame($result, $this->model->getAmount($amount));
    }
}
