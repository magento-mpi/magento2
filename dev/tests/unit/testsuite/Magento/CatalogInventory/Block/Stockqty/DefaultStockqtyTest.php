<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Block\Stockqty;

/**
 * Unit test for DefaultStockqty
 */
class DefaultStockqtyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogInventory\Block\Stockqty\DefaultStockqty
     */
    protected $block;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\CatalogInventory\Service\V1\StockItemService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemService;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->registryMock = $this->getMock('Magento\Framework\Registry', array(), array(), '', false);
        $this->stockItemService = $this->getMock(
            'Magento\CatalogInventory\Service\V1\StockItemService',
            [],
            [],
            '',
            false
        );

        $this->block = $objectManager->getObject(
            'Magento\CatalogInventory\Block\Stockqty\DefaultStockqty',
            array('registry' => $this->registryMock, 'stockItemService' => $this->stockItemService)
        );
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $productTags = array('catalog_product_1');
        $product = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $product->expects($this->once())->method('getIdentities')->will($this->returnValue($productTags));
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('current_product')
            ->will($this->returnValue($product));
        $this->assertEquals($productTags, $this->block->getIdentities());
    }

    /**
     * @param int $productStockQty
     * @param int|null $productId
     * @param int|null $dataQty
     * @param int $expectedQty
     * @dataProvider getStockQtyDataProvider
     */
    public function testGetStockQty($productStockQty, $productId, $dataQty, $expectedQty)
    {
        $this->assertNull($this->block->getData('product_stock_qty'));
        if ($dataQty) {
            $this->setDataArrayValue('product_stock_qty', $dataQty);
        } else {
            $product = $this->getMock('Magento\Catalog\Model\Product', ['getId', '__wakeup'], [], '', false);
            $product->expects($this->any())->method('getId')->will($this->returnValue($productId));

            $this->registryMock->expects($this->any())
                ->method('registry')
                ->with('current_product')
                ->will($this->returnValue($product));

            if ($productId) {
                $this->stockItemService->expects($this->once())
                    ->method('getStockQty')
                    ->with($this->equalTo($productId))
                    ->will($this->returnValue($productStockQty));
            }
        }
        $this->assertSame($expectedQty, $this->block->getStockQty());
        $this->assertSame($expectedQty, $this->block->getData('product_stock_qty'));
    }

    public function testGetStockQtyLeft()
    {
        $productMinQty = 2;
        $treshold = 2;
        $productStockQty = 3;
        $productId = 4;
        $expectedQty = 1;

        $product = $this->getMock('Magento\Catalog\Model\Product', ['getId', '__wakeup'], [], '', false);
        $product->expects($this->any())->method('getId')->will($this->returnValue($productId));

        $this->setDataArrayValue('threshold_qty', $treshold);

        $this->registryMock->expects($this->any())
            ->method('registry')
            ->with('current_product')
            ->will($this->returnValue($product));


        $this->stockItemService->expects($this->once())
            ->method('getStockQty')
            ->with($this->equalTo($productId))
            ->will($this->returnValue($productStockQty));

        $stockItem = $this->getMock(
            'Magento\CatalogInventory\Service\V1\Data\StockItem',
            [],
            [],
            '',
            false
        );

        $stockItem->expects($this->once())
            ->method('getMinQty')
            ->will($this->returnValue($productMinQty));

        $this->stockItemService->expects($this->once())
            ->method('getStockItem')
            ->with($this->equalTo($productId))
            ->will($this->returnValue($stockItem));

        $this->assertSame($expectedQty, $this->block->getStockQtyLeft());
    }

    /**
     * @return array
     */
    public function getStockQtyDataProvider()
    {
        return [
            [
                'product qty' => 100,
                'product id' => 5,
                'default qty' => null,
                'expected qty' => 100
            ],
            [
                'product qty' => 100,
                'product id' => null,
                'default qty' => null,
                'expected qty' => 0
            ],
            [
                'product qty' => null,
                'product id' => null,
                'default qty' => 50,
                'expected qty' => 50
            ],
        ];
    }

    /**
     * @param string $key
     * @param string|float|int $value
     */
    protected function setDataArrayValue($key, $value)
    {
        $property = new \ReflectionProperty($this->block, '_data');
        $property->setAccessible(true);
        $dataArray = $property->getValue($this->block);
        $dataArray[$key] = $value;
        $property->setValue($this->block, $dataArray);
    }
}
