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
    protected $saleableItem;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Pricing\PriceComposite
     */
    protected $prices;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Pricing\Adjustment\Collection
     */
    protected $adjustmentCollection;

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
        $this->saleableItem = $this->getMock('Magento\Pricing\Object\SaleableInterface', [], [], '', false);
        $this->prices = $this->getMock('Magento\Pricing\Price\Collection', [], [], '', false);
        $this->adjustmentCollection = $this->getMock('Magento\Pricing\Adjustment\Collection', [], [], '', false);
        $this->amountFactory = $this->getMock('Magento\Pricing\Amount\AmountFactory', [], [], '', false);
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
     * test method getPrices()
     */
    public function testGetPrices()
    {
        $this->assertEquals($this->prices, $this->model->getPrices());
    }

    /**
     * @param $entryParams
     * @param $createCount
     * @dataProvider providerGetPrice
     */
    public function testGetPrice($entryParams, $createCount)
    {
        $priceCode= current(array_values(reset($entryParams)));

        $this->prices
            ->expects($this->exactly($createCount))
            ->method('get')
            ->with($this->equalTo($priceCode))
            ->will($this->returnValue('basePrice'));

        foreach ($entryParams as $params) {
            list($priceCode) = array_values($params);
            $this->assertEquals('basePrice', $this->model->getPrice($priceCode));
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
                    ['priceCode' => 'testCode']
                ],
                'createCount' => 1
            ],
            'case with existing price' => [
                'entryParams' => [
                    ['priceCode' => 'testCode'],
                    ['priceCode' => 'testCode']
                ],
                'createCount' => 2
            ],
            'case with quantity' => [
                'entryParams' => [
                    ['priceCode' => 'testCode']
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
        $this->adjustmentCollection->expects($this->once())->method('getItems')->will($this->returnValue('result'));
        $this->assertEquals('result', $this->model->getAdjustments());
    }

    /**
     * @covers \Magento\Pricing\PriceInfo\Base::getAdjustment
     */
    public function testGetAdjustment()
    {
        $this->adjustmentCollection->expects($this->any())->method('getItemByCode')
            ->with('test1')
            ->will($this->returnValue('adjustment'));
        $this->assertEquals('adjustment', $this->model->getAdjustment('test1'));
    }
}
