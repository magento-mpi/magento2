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

namespace Magento\Pricing\Price;

/**
 * Test for class Collection
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Pricing\Price\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Pricing\Price\Pool
     */
    protected $pool;

    /**
     * @var \Magento\Pricing\Price\PriceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceMock;

    /**
     * @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableItemMock;

    /**
     * @var \Magento\Pricing\Price\Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $factoryMock;

    /**
     * @var float
     */
    protected $quantity;

    /**
     * Test setUp
     */
    public function setUp()
    {
        $this->pool = new Pool(
            [
                'regular_price' => 'RegularPrice',
                'special_price' => 'SpecialPrice',
                'group_price' => 'GroupPrice'
            ]
        );

        $this->saleableItemMock = $this->getMockForAbstractClass('Magento\Pricing\Object\SaleableInterface');
        $this->priceMock = $this->getMockForAbstractClass('Magento\Pricing\Price\PriceInterface');
        $this->factoryMock = $this->getMock('Magento\Pricing\Price\Factory', [], [], '', false);

        $this->collection = new Collection(
            $this->saleableItemMock,
            $this->factoryMock,
            $this->pool,
            $this->quantity

        );
    }

    /**
     * Test get method
     */
    public function testGet()
    {
        $this->factoryMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo($this->saleableItemMock),
                $this->equalTo('RegularPrice'),
                $this->quantity
            )
            ->will($this->returnValue($this->priceMock));
        $this->assertEquals($this->priceMock, $this->collection->get('regular_price'));
    }

    /**
     * Test current method
     */
    public function testCurrent()
    {
        $this->factoryMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo($this->saleableItemMock),
                $this->equalTo($this->pool->current()),
                $this->quantity
            )
            ->will($this->returnValue($this->priceMock));
        $this->assertEquals($this->priceMock, $this->collection->current());
    }
}
