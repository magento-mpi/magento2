<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1;

use Magento\CatalogInventory\Model\Stock\ItemRegistry;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;

/**
 * Class StockItemTest
 */
class StockItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StockItemService
     */
    protected $model;

    /**
     * @var ItemRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemRegistry;

    /**
     * @var ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * @var Data\StockItemBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemBuilder;

    /**
     * @var \Magento\Catalog\Service\V1\Product\Link\ProductLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productLoader;

    protected function setUp()
    {
        $this->stockItemRegistry = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\ItemRegistry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->config = $this->getMockBuilder('Magento\Catalog\Model\ProductTypes\ConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockItemBuilder = $this->getMockBuilder('Magento\CatalogInventory\Service\V1\Data\StockItemBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->productLoader = $this->getMockBuilder('Magento\Catalog\Service\V1\Product\Link\ProductLoader')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\CatalogInventory\Service\V1\StockItemService',
            [
                'stockItemRegistry' => $this->stockItemRegistry,
                'config' => $this->config,
                'stockItemBuilder' => $this->stockItemBuilder,
                'productLoader' => $this->productLoader
            ]
        );
    }

    public function testGetStockItem()
    {
        $productId = 123;
        $stockItemData = ['some_key' => 'someValue'];

        $stockItemModel = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $stockItemModel->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($stockItemData));

        $this->stockItemRegistry->expects($this->once())
            ->method('retrieve')
            ->with($productId)
            ->will($this->returnValue($stockItemModel));

        $this->stockItemBuilder->expects($this->once())
            ->method('populateWithArray')
            ->with($stockItemData);

        $stockItemDo = $this->getMockBuilder('Magento\CatalogInventory\Service\V1\Data\StockItem')
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockItemBuilder->expects($this->once())
            ->method('create')
            ->will($this->returnValue($stockItemDo));

        $this->assertEquals($stockItemDo, $this->model->getStockItem($productId));
    }

    public function testSaveStockItem()
    {
        $productId = 123;
        $stockItemData = ['some_key' => 'someValue'];

        $stockItemDo = $this->getMockBuilder('Magento\CatalogInventory\Service\V1\Data\StockItem')
            ->disableOriginalConstructor()
            ->getMock();
        $stockItemDo->expects($this->once())
            ->method('getProductId')
            ->will($this->returnValue($productId));
        $stockItemDo->expects($this->once())
            ->method('__toArray')
            ->will($this->returnValue($stockItemData));

        $stockItemModel = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $stockItemModel->expects($this->once())
            ->method('setData')
            ->with($stockItemData);
        $stockItemModel->expects($this->once())
            ->method('save');

        $this->stockItemRegistry->expects($this->once())
            ->method('retrieve')
            ->with($productId)
            ->will($this->returnValue($stockItemModel));

        $this->assertEquals($this->model, $this->model->saveStockItem($stockItemDo));
    }

    public function testSubtractQty()
    {
        $productId = 123;
        $qty = 1.5;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('subtractQty')
            ->with($qty);

        $this->assertEquals($this->model, $this->model->subtractQty($productId, $qty));
    }

    public function testCanSubtractQty()
    {
        $productId = 23;
        $result = false;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('canSubtractQty')
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->canSubtractQty($productId));
    }

    public function testAddQty()
    {
        $productId = 143;
        $qty = 3.5;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('addQty')
            ->with($qty);

        $this->assertEquals($this->model, $this->model->addQty($productId, $qty));
    }

    public function testGetMinQty()
    {
        $productId = 53;
        $result = 3;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('getMinQty')
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->getMinQty($productId));
    }

    public function testGetMinSaleQty()
    {
        $productId = 51;
        $result = 2;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('getMinSaleQty')
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->getMinSaleQty($productId));
    }

    public function testGetMaxSaleQty()
    {
        $productId = 46;
        $result = 15;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('getMaxSaleQty')
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->getMaxSaleQty($productId));
    }

    public function testGetNotifyStockQty()
    {
        $productId = 12;
        $result = 15.3;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('getNotifyStockQty')
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->getNotifyStockQty($productId));
    }

    public function testEnableQtyIncrements()
    {
        $productId = 48;
        $result = true;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('getEnableQtyIncrements')
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->getEnableQtyIncrements($productId));
    }

    public function testGetQtyIncrements()
    {
        $productId = 25;
        $result = 15;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('getQtyIncrements')
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->getQtyIncrements($productId));
    }

    public function testGetBackorders()
    {
        $productId = 34;
        $result = 2;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('getBackorders')
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->getBackorders($productId));
    }

    public function testGetManageStock()
    {
        $productId = 32;
        $result = 3;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('getManageStock')
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->getManageStock($productId));
    }

    public function testGetCanBackInStock()
    {
        $productId = 59;
        $result = false;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('getCanBackInStock')
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->getCanBackInStock($productId));
    }

    public function testCheckQty()
    {
        $productId = 143;
        $qty = 3.5;
        $result = false;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('checkQty')
            ->with($qty)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->checkQty($productId, $qty));
    }

    public function testSuggestQty()
    {
        $productId = 143;
        $qty = 3.5;
        $result = true;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('suggestQty')
            ->with($qty)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->suggestQty($productId, $qty));
    }

    public function testCheckQuoteItemQty()
    {
        $productId = 143;
        $qty = 3.5;
        $summaryQty = 4;
        $origQty = 1;
        $result = $this->getMock('Magento\Framework\Object');

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('checkQuoteItemQty')
            ->with($qty, $summaryQty, $origQty)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->checkQuoteItemQty($productId, $qty, $summaryQty, $origQty));
    }

    public function testVerifyStock()
    {
        $productId = 143;
        $qty = 2.5;
        $result = true;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('verifyStock')
            ->with($qty)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->verifyStock($productId, $qty));
    }

    public function testVerifyNotification()
    {
        $productId = 42;
        $qty = 7.3;
        $result = true;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('verifyNotification')
            ->with($qty)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->verifyNotification($productId, $qty));
    }

    public function testGetIsInStock()
    {
        $productId = 96;
        $result = false;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('getIsInStock')
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->getIsInStock($productId));
    }

    public function testGetStockQty()
    {
        $productId = 34;
        $result = 3;

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('getStockQty')
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->getStockQty($productId));
    }

    public function testCheckQtyIncrements()
    {
        $productId = 86;
        $qty = 6;
        $result = $this->getMock('Magento\Framework\Object');

        $stockItemModel = $this->getStockItemModel($productId);
        $stockItemModel->expects($this->once())
            ->method('checkQtyIncrements')
            ->with($qty)
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->model->checkQtyIncrements($productId, $qty));
    }

    public function testIsQty()
    {
        $configAll = [
            1 => ['is_qty' => true],
            2 => ['is_qty' => false],
            3 => []
        ];
        $this->config->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue($configAll));

        $this->assertTrue($this->model->isQty(1));
        $this->assertFalse($this->model->isQty(2));
        $this->assertFalse($this->model->isQty(3));
        $this->assertFalse($this->model->isQty(4));
    }

    public function testGetIsQtyTypeIds()
    {
        $configAll = [
            1 => ['is_qty' => true],
            2 => ['is_qty' => false],
            3 => []
        ];
        $resultAll = [1 => true, 2 => false, 3 => false];
        $resultTrue = [1 => true];
        $resultFalse = [2 => false, 3 => false];

        $this->config->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue($configAll));

        $this->assertEquals($resultAll, $this->model->getIsQtyTypeIds());
        $this->assertEquals($resultTrue, $this->model->getIsQtyTypeIds(true));
        $this->assertEquals($resultFalse, $this->model->getIsQtyTypeIds(false));
    }

    /**
     * @param string $productSku
     * @param int $productId
     * @param [] $stockItemData
     * @dataProvider getStockItemBySkuDataProvider
     */
    public function testGetStockItemBySku($productSku, $productId, $stockItemData)
    {
        // 1. Get mocks
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\CatalogInventory\Model\Stock\Item|\PHPUnit_Framework_MockObject_MockObject $stockItem */
        $stockItem = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Item')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Data\StockItem|\PHPUnit_Framework_MockObject_MockObject $stockItemDataObject */
        $stockItemDataObject = $this->getMockBuilder('Magento\CatalogInventory\Service\V1\Data\StockItem')
            ->disableOriginalConstructor()
            ->getMock();

        // 2. Set fixtures
        $product->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $stockItem->expects($this->any())->method('getData')->will($this->returnValue($stockItemData));

        $this->productLoader->expects($this->any())->method('load')->will($this->returnValueMap([
            [$productSku, $product]
        ]));

        $this->stockItemRegistry->expects($this->any())->method('retrieve')->will($this->returnValueMap([
            [$productId, $stockItem]
        ]));

        $this->stockItemBuilder->expects($this->any())
            ->method('create')
            ->will($this->returnValue($stockItemDataObject));

        // 3. Set expectations
        $this->stockItemBuilder->expects($this->any())->method('populateWithArray')->with($stockItemData);

        // 4. Run tested method
        $result = $this->model->getStockItemBySku($productSku);

        // 5. Compare actual result with expected result
        $this->assertEquals($stockItemDataObject, $result);
    }

    /**
     * @return array
     */
    public function getStockItemBySkuDataProvider()
    {
        return [
            ['sku1', 1, ['stock_item_id' => 123]],
            ['sku1', 1, []],
        ];
    }

    /**
     * @param string $productSku
     * @param int $productId
     * @dataProvider getStockItemBySkuWithExceptionDataProvider
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetStockItemBySkuWithException($productSku, $productId)
    {
        // 1. Get mocks
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        // 2. Set fixtures
        $this->productLoader->expects($this->any())->method('load')->will($this->returnValueMap([
            [$productSku, $product]
        ]));
        $product->expects($this->any())->method('getId')->will($this->returnValue($productId));

        // 3. Run tested method
        $this->model->getStockItemBySku($productSku);
    }

    /**
     * @return array
     */
    public function getStockItemBySkuWithExceptionDataProvider()
    {
        return [
            ['sku1', null],
            ['sku1', false],
            ['sku1', 0],
        ];
    }

    /**
     * @param string $productSku
     * @param int $productId
     * @param array $stockItemData
     * @param array $stockItemDetailsDoData
     * @param array $dataToSave
     * @param int $savedStockItemId
     * @dataProvider saveStockItemBySkuDataProvider
     */
    public function testSaveStockItemBySku(
        $productSku,
        $productId,
        $stockItemData,
        $stockItemDetailsDoData,
        $dataToSave,
        $savedStockItemId
    )
    {
        // 1. Create mocks
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\CatalogInventory\Model\Stock\Item|\PHPUnit_Framework_MockObject_MockObject $stockItem */
        $stockItem = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Item')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Data\StockItem|\PHPUnit_Framework_MockObject_MockObject $stockItemDataObject */
        $stockItemDataObject = $this->getMockBuilder('Magento\CatalogInventory\Service\V1\Data\StockItem')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Data\StockItem|\PHPUnit_Framework_MockObject_MockObject $stockItemDataObjectMerged */
        $stockItemDataObjectMerged = $this->getMockBuilder('Magento\CatalogInventory\Service\V1\Data\StockItem')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Data\StockItemDetails|\PHPUnit_Framework_MockObject_MockObject $stockItemDetailsDo */
        $stockItemDetailsDo = $this->getMockBuilder('Magento\CatalogInventory\Service\V1\Data\StockItemDetails')
            ->disableOriginalConstructor()
            ->getMock();

        // 2. Set fixtures
        $product->expects($this->any())->method('getId')->will($this->returnValue($productId));

        $stockItem->expects($this->any())->method('getData')->will($this->returnValue($stockItemData));
        $stockItem->expects($this->any())->method('save')->will($this->returnSelf());
        $stockItem->expects($this->any())->method('getId')->will($this->returnValue($savedStockItemId));

        $this->productLoader->expects($this->any())->method('load')->will($this->returnValueMap([
            [$productSku, $product]
        ]));

        $this->stockItemRegistry->expects($this->any())->method('retrieve')->will($this->returnValueMap([
            [$productId, $stockItem]
        ]));

        $this->stockItemBuilder->expects($this->any())
            ->method('create')
            ->will($this->returnValue($stockItemDataObject));

        $stockItemDetailsDo->expects($this->any())
            ->method('__toArray')
            ->will($this->returnValue($stockItemDetailsDoData));

        $this->stockItemBuilder->expects($this->any())
            ->method('mergeDataObjectWithArray')
            ->will($this->returnValue($stockItemDataObjectMerged));

        $stockItemDataObjectMerged->expects($this->any())
            ->method('__toArray')
            ->will($this->returnValue($dataToSave));

        // 3. Set expectations
        $stockItem->expects($this->any())->method('setData')->with($dataToSave)->will($this->returnSelf());
        $this->stockItemBuilder->expects($this->any())
            ->method('populateWithArray')
            ->with($stockItemData)
            ->will($this->returnSelf());

        // 4. Run tested method
        $result = $this->model->saveStockItemBySku($productSku, $stockItemDetailsDo);

        // 5. Compare actual result with expected result
        $this->assertEquals($savedStockItemId, $result);
    }

    /**
     * @return array
     */
    public function saveStockItemBySkuDataProvider()
    {
        return [
            ['sku1', 1, ['key1' => 'value1'], ['key2' => 'value2'], ['key3' => 'value3'], 123],
            ['sku1', 1, [], [], [], 123],
        ];
    }

    /**
     * @param string $productSku
     * @param int $productId
     * @dataProvider saveStockItemBySkuWithExceptionDataProvider
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testSaveStockItemBySkuWithException($productSku, $productId)
    {
        // 1. Get mocks
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Data\StockItemDetails|\PHPUnit_Framework_MockObject_MockObject $stockItemDetailsDo */
        $stockItemDetailsDo = $this->getMockBuilder('Magento\CatalogInventory\Service\V1\Data\StockItemDetails')
            ->disableOriginalConstructor()
            ->getMock();

        // 2. Set fixtures
        $this->productLoader->expects($this->any())->method('load')->will($this->returnValueMap([
            [$productSku, $product]
        ]));
        $product->expects($this->any())->method('getId')->will($this->returnValue($productId));

        // 3. Run tested method
        $this->model->saveStockItemBySku($productSku, $stockItemDetailsDo);
    }

    /**
     * @return array
     */
    public function saveStockItemBySkuWithExceptionDataProvider()
    {
        return [
            ['sku1', null],
            ['sku1', false],
            ['sku1', 0],
        ];
    }

    /**
     * @param int $productId
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getStockItemModel($productId)
    {
        $stockItemModel = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $this->stockItemRegistry->expects($this->once())
            ->method('retrieve')
            ->with($productId)
            ->will($this->returnValue($stockItemModel));

        return $stockItemModel;
    }
}
