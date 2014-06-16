<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model;

use Magento\CatalogInventory\Model\Resource\Stock\Item\CollectionFactory;

/**
 * Class StockTest
 */
class StockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Stock
     */
    protected $model;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockStatus;

    /**
     * @var CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactory;

    protected function setUp()
    {
        $this->collectionFactory = $this
            ->getMockBuilder('Magento\CatalogInventory\Model\Resource\Stock\Item\CollectionFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockStatus = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Status')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\CatalogInventory\Model\Stock',
            [
                'stockStatus' => $this->stockStatus,
                'collectionFactory' => $this->collectionFactory
            ]
        );
    }

    public function testAddItemsToProducts()
    {
        $storeId = 3;
        $productOneId = 1;
        $productOneStatus = \Magento\CatalogInventory\Model\Stock\Status::STATUS_IN_STOCK;
        $productTwoId = 2;
        $productThreeId = 3;

        $stockItemProductId = $productOneId;
        $stockItemStockId = \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID;

        $productCollection = $this->getMockBuilder('Magento\Catalog\Model\Resource\Product\Collection')
            ->disableOriginalConstructor()
            ->setMethods(['getStoreId', 'getIterator'])
            ->getMock();

        $stockItem = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $stockItem->expects($this->atLeastOnce())
            ->method('getProductId')
            ->will($this->returnValue($stockItemProductId));
        $stockItem->expects($this->atLeastOnce())
            ->method('getStockId')
            ->will($this->returnValue($stockItemStockId));

        $itemCollection = $this->getMockBuilder('Magento\CatalogInventory\Model\Resource\Stock\Item\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $itemCollection->expects($this->atLeastOnce())
            ->method('addStockFilter')
            ->with(Stock::DEFAULT_STOCK_ID)
            ->will($this->returnSelf());
        $itemCollection->expects($this->atLeastOnce())
            ->method('addProductsFilter')
            ->with($productCollection)
            ->will($this->returnSelf());
        $itemCollection->expects($this->atLeastOnce())
            ->method('joinStockStatus')
            ->with($storeId)
            ->will($this->returnSelf());
        $itemCollection->expects($this->atLeastOnce())
            ->method('load')
            ->will($this->returnValue([$stockItem]));

        $this->collectionFactory->expects($this->atLeastOnce())
            ->method('create')
            ->will($this->returnValue($itemCollection));


        $productOne = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getStockStatus', '__wakeup'])
            ->getMock();
        $productOne->expects($this->atLeastOnce())
            ->method('getId')
            ->will($this->returnValue($productOneId));
        $productOne->expects($this->atLeastOnce())
            ->method('getStockStatus')
            ->will($this->returnValue($productOneStatus));
        $productTwo = $this->getMockBuilder('Magento\Catalog\Model\Product')->disableOriginalConstructor()->getMock();
        $productTwo->expects($this->atLeastOnce())->method('getId')->will($this->returnValue($productTwoId));
        $productThree = $this->getMockBuilder('Magento\Catalog\Model\Product')->disableOriginalConstructor()->getMock();
        $productThree->expects($this->atLeastOnce())->method('getId')->will($this->returnValue($productThreeId));

        $productCollection->expects($this->atLeastOnce())->method('getStoreId')->will($this->returnValue($storeId));
        $productCollection->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$productOne, $productTwo, $productThree])));


        $this->stockStatus->expects($this->once())
            ->method('assignProduct')
            ->with($productOne, $stockItemStockId, $productOneStatus);

        $this->assertEquals($this->model, $this->model->addItemsToProducts($productCollection));
    }
}
