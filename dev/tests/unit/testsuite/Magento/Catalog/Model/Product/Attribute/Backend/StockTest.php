<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Attribute\Backend;

class StockTest extends \PHPUnit_Framework_TestCase
{
    const ATTRIBUTE_NAME = 'quantity_and_stock_status';

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Backend\Stock
     */
    protected $model;

    /**
     * @var \Magento\CatalogInventory\Service\V1\StockItemServiceInterface
     */
    protected $stockItemService;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectHelper;

    protected function setUp()
    {
        $this->objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->stockItemService = $this->getMockBuilder('Magento\CatalogInventory\Service\V1\StockItemService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $this->objectHelper->getObject(
            'Magento\Catalog\Model\Product\Attribute\Backend\Stock',
            array('stockItemService' => $this->stockItemService)
        );
        $attribute = $this->getMock('Magento\Framework\Object', array('getAttributeCode'));
        $attribute->expects($this->atLeastOnce())
            ->method('getAttributeCode')
            ->will($this->returnValue(self::ATTRIBUTE_NAME));
        $this->model->setAttribute($attribute);
    }

    public function testAfterLoad()
    {
        $productId = 2;
        $stockItemDo = $this->getMockBuilder('Magento\CatalogInventory\Service\V1\Data\StockItem')
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockItemService->expects($this->once())
            ->method('getStockItem')
            ->with($productId)
            ->will($this->returnValue($stockItemDo));

        $stockItemDo->expects($this->once())->method('getIsInStock')->will($this->returnValue(1));
        $stockItemDo->expects($this->once())->method('getQty')->will($this->returnValue(5));
        $object = new \Magento\Framework\Object(['id' => $productId]);
        $this->model->afterLoad($object);
        $data = $object->getData();
        $this->assertEquals(1, $data[self::ATTRIBUTE_NAME]['is_in_stock']);
        $this->assertEquals(5, $data[self::ATTRIBUTE_NAME]['qty']);
    }

    public function testBeforeSave()
    {
        $object = new \Magento\Framework\Object(
            array(
                self::ATTRIBUTE_NAME => array('is_in_stock' => 1, 'qty' => 5),
                'stock_data' => array('is_in_stock' => 2, 'qty' => 2)
            )
        );
        $stockData = $object->getStockData();
        $this->assertEquals(2, $stockData['is_in_stock']);
        $this->assertEquals(2, $stockData['qty']);
        $this->assertNotEmpty($object->getData(self::ATTRIBUTE_NAME));

        $this->model->beforeSave($object);

        $stockData = $object->getStockData();
        $this->assertEquals(1, $stockData['is_in_stock']);
        $this->assertEquals(5, $stockData['qty']);
        $this->assertNull($object->getData(self::ATTRIBUTE_NAME));
    }

    public function testBeforeSaveQtyIsEmpty()
    {
        $object = new \Magento\Framework\Object(
            array(
                self::ATTRIBUTE_NAME => array('is_in_stock' => 1, 'qty' => ''),
                'stock_data' => array('is_in_stock' => 2, 'qty' => '')
            )
        );

        $this->model->beforeSave($object);

        $stockData = $object->getStockData();
        $this->assertNull($stockData['qty']);
    }

    public function testBeforeSaveQtyIsZero()
    {
        $object = new \Magento\Framework\Object(
            array(
                self::ATTRIBUTE_NAME => array('is_in_stock' => 1, 'qty' => 0),
                'stock_data' => array('is_in_stock' => 2, 'qty' => 0)
            )
        );

        $this->model->beforeSave($object);

        $stockData = $object->getStockData();
        $this->assertEquals(0, $stockData['qty']);
    }
}
