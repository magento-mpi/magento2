<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model;

/**
 * Class ObserverTest
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Observer
     */
    protected $model;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\ItemRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemRegistry;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockStatus;

    /**
     * @var \Magento\CatalogInventory\Model\StockFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockFactory;

    /**
     * @var \Magento\Framework\Event\Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventObserver;

    /**
     * @var \Magento\Framework\Event|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $event;

    protected function setUp()
    {
        $this->stockItemRegistry = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\ItemRegistry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockStatus = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Status')
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockFactory = $this->getMockBuilder('Magento\CatalogInventory\Model\StockFactory')
            ->setMethods(['create'])
            ->getMock();

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\CatalogInventory\Model\Observer',
            [
                'stockItemRegistry' => $this->stockItemRegistry,
                'stockStatus' => $this->stockStatus,
                'stockFactory' => $this->stockFactory
            ]
        );

        $this->event = $this->getMockBuilder('Magento\Framework\Event')
            ->disableOriginalConstructor()
            ->setMethods(['getProduct', 'getCollection'])
            ->getMock();

        $this->eventObserver = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->setMethods(['getEvent'])
            ->getMock();
        $this->eventObserver->expects($this->atLeastOnce())
            ->method('getEvent')
            ->will($this->returnValue($this->event));
    }

    public function testAddInventoryData()
    {
        $productId = 4;
        $stockId = 6;
        $stockStatus = true;

        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getStockStatus', '__wakeup'])
            ->getMock();
        $product->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($productId));
        $product->expects($this->once())
            ->method('getStockStatus')
            ->will($this->returnValue($stockStatus));

        $this->event->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($product));

        $stockItem = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $stockItem->expects($this->once())
            ->method('getStockId')
            ->will($this->returnValue($stockId));

        $this->stockItemRegistry->expects($this->once())
            ->method('retrieve')
            ->with($productId)
            ->will($this->returnValue($stockItem));

        $this->stockStatus->expects($this->once())
            ->method('assignProduct')
            ->with($product, $stockId, $stockStatus)
            ->will($this->returnSelf());

        $this->assertEquals($this->model, $this->model->addInventoryData($this->eventObserver));
    }

    public function testAddStockStatusToCollection()
    {
        $requireStockItems = false;

        $productCollection = $this->getMockBuilder('Magento\Catalog\Model\Resource\Product\Collection')
            ->disableOriginalConstructor()
            ->setMethods(['hasFlag'])
            ->getMock();
        $this->event->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($productCollection));

        $productCollection->expects($this->once())
            ->method('hasFlag')
            ->with('require_stock_items')
            ->will($this->returnValue($requireStockItems));

        $this->stockStatus->expects($this->once())
            ->method('addStockStatusToProducts')
            ->with($productCollection)
            ->will($this->returnSelf());

        $this->assertEquals($this->model, $this->model->addStockStatusToCollection($this->eventObserver));
    }

    public function testAddStockStatusToCollectionRequireStockItems()
    {
        $requireStockItems = true;

        $productCollection = $this->getMockBuilder('Magento\Catalog\Model\Resource\Product\Collection')
            ->disableOriginalConstructor()
            ->setMethods(['hasFlag'])
            ->getMock();
        $this->event->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($productCollection));

        $productCollection->expects($this->once())
            ->method('hasFlag')
            ->with('require_stock_items')
            ->will($this->returnValue($requireStockItems));

        $stock = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock')
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($stock));

        $stock->expects($this->once())
            ->method('addItemsToProducts')
            ->with($productCollection)
            ->will($this->returnSelf());

        $this->assertEquals($this->model, $this->model->addStockStatusToCollection($this->eventObserver));
    }
}
