<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Model\Resource;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Model\Resource\Item
     */
    protected $resourceModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $appResource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eqvModelConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeSet;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formatLocale;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $validatorFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderItemCollection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productTypesConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $adminItem;

    protected function setUp()
    {
        $this->appResource = $this->getMockBuilder('Magento\Framework\App\Resource')
            ->disableOriginalConstructor()
            ->getMock();
        $this->eqvModelConfig = $this->getMockBuilder('Magento\Eav\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeSet = $this->getMockBuilder('Magento\Eav\Model\Entity\Attribute\Set')
            ->disableOriginalConstructor()
            ->getMock();
        $this->formatLocale = $this->getMockBuilder('Magento\Framework\Locale\Format')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resourceHelper = $this->getMockBuilder('Magento\Eav\Model\Resource\Helper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->validatorFactory = $this->getMockBuilder('Magento\Framework\Validator\UniversalFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->rmaHelper = $this->getMockBuilder('Magento\Rma\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderItemCollection = $this->getMockBuilder('Magento\Sales\Model\Resource\Order\Item\CollectionFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->productFactory = $this->getMockBuilder('Magento\Catalog\Model\ProductFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->productTypesConfig = $this->getMockBuilder('Magento\Catalog\Model\ProductTypes\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->adminItem = $this->getMockBuilder('Magento\Sales\Model\Order\Admin\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $data = [];

        $this->resourceModel = new \Magento\Rma\Model\Resource\Item(
            $this->appResource,
            $this->eqvModelConfig,
            $this->attributeSet,
            $this->formatLocale,
            $this->resourceHelper,
            $this->validatorFactory,
            $this->rmaHelper,
            $this->orderItemCollection,
            $this->productFactory,
            $this->productTypesConfig,
            $this->adminItem,
            $data
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('Magento\Rma\Model\Resource\Item', $this->resourceModel);
    }

    public function testGetReturnableItems()
    {
        $items = [5 => 3];
        $adapterMock = $this->getAdapterMock($items);
        $this->resourceModel->setConnection($adapterMock);
        $orderId = 1000001;
        $result = $this->resourceModel->getReturnableItems($orderId);
        $this->assertEquals($items, $result);
    }

    public function testGetOrderItemsNoItems()
    {
        $orderId = 10000001;

        $readMock = $this->getMockBuilder('Magento\Framework\DB\Adapter\Pdo\Mysql')
            ->disableOriginalConstructor()
            ->getMock();
        $readMock->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnArgument(0));

        $this->resourceModel->setConnection($readMock);

        $expression = new \Zend_Db_Expr('(qty_shipped - qty_returned)');

        $orderItemsCollectionMock = $this->getMockBuilder('Magento\Sales\Model\Resource\Order\Item\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $orderItemsCollectionMock->expects($this->once())
            ->method('addExpressionFieldToSelect')
            ->with('available_qty', $expression, ['qty_shipped', 'qty_returned'])
            ->will($this->returnSelf());
        $orderItemsCollectionMock->expects($this->any())
            ->method('addFieldToFilter')
            ->will($this->returnSelf());
        $orderItemsCollectionMock->expects($this->once())
            ->method('count')
            ->will($this->returnValue(0));

        $this->orderItemCollection->expects($this->once())
            ->method('create')
            ->will($this->returnValue($orderItemsCollectionMock));

        $result = $this->resourceModel->getOrderItems($orderId);
        $this->assertEquals($orderItemsCollectionMock, $result);
    }

    public function testGetOrderItemsRemoveByParent()
    {
        $orderId = 10000001;
        $excludeId = 5;
        $parentId = 6;
        $itemId = 1;

        $readMock = $this->getAdapterMock([$itemId => 1]);

        $this->resourceModel->setConnection($readMock);

        $orderItemsCollectionMock = $this->prepareOrderItemCollectionMock();

        $this->orderItemCollection->expects($this->once())
            ->method('create')
            ->will($this->returnValue($orderItemsCollectionMock));

        $parentItemMock = $this->getMockBuilder('Magento\Sales\Model\Order\Item')
            ->disableOriginalConstructor()
            ->setMethods(['getParentItemId', 'getId', '__wakeup'])
            ->getMock();
        $parentItemMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($itemId));
        $parentItemMock->expects($this->any())
            ->method('getParentItemId')
            ->will($this->returnValue($parentId));

        $iterator = new \ArrayIterator([$parentItemMock]);

        $orderItemsCollectionMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iterator));

        $result = $this->resourceModel->getOrderItems($orderId, $excludeId);
        $this->assertEquals($orderItemsCollectionMock, $result);
    }

    public function testGetOrderItemsCanReturnNotEmpty()
    {
        $orderId = 10000001;
        $itemId = 1;
        $fetchData = [$itemId => 2];
        $storeId = 1;

        $readMock = $this->getAdapterMock($fetchData);

        $this->resourceModel->setConnection($readMock);

        $orderItemsCollectionMock = $this->prepareOrderItemCollectionMock();

        $this->orderItemCollection->expects($this->once())
            ->method('create')
            ->will($this->returnValue($orderItemsCollectionMock));

        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $this->productFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($productMock));

        $itemMockCanReturn = $this->prepareOrderItemMock($itemId, $storeId);

        $iterator = new \ArrayIterator([$itemMockCanReturn]);

        $orderItemsCollectionMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iterator));

        $this->rmaHelper->expects($this->at(0))
            ->method('canReturnProduct')
            ->with($this->equalTo($productMock), $this->equalTo($storeId))
            ->will($this->returnValue(true));

        $returnableItems = $this->resourceModel->getReturnableItems($orderId);
        $result = $this->resourceModel->getOrderItems($orderId);

        foreach ($result as $item) {
            $this->assertEquals($item->getAvailableQty(), $returnableItems[$item->getId()]);
        }
        $this->assertEquals($orderItemsCollectionMock, $result);
    }

    public function testGetOrderItemsCanReturnEmpty()
    {
        $orderId = 10000001;
        $itemId = 1;
        $fetchData = [];
        $storeId = 1;

        $readMock = $this->getAdapterMock($fetchData);

        $this->resourceModel->setConnection($readMock);

        $orderItemsCollectionMock = $this->prepareOrderItemCollectionMock();

        $this->orderItemCollection->expects($this->once())
            ->method('create')
            ->will($this->returnValue($orderItemsCollectionMock));

        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $this->productFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($productMock));

        $itemMockCanReturn = $this->prepareOrderItemMock($itemId, $storeId);

        $iterator = new \ArrayIterator([$itemMockCanReturn]);

        $orderItemsCollectionMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iterator));

        $this->rmaHelper->expects($this->at(0))
            ->method('canReturnProduct')
            ->with($this->equalTo($productMock), $this->equalTo($storeId))
            ->will($this->returnValue(true));

        $result = $this->resourceModel->getOrderItems($orderId);
        $this->assertEquals($orderItemsCollectionMock, $result);
    }

    public function testGetOrderItemsCanReturn()
    {
        $orderId = 10000001;
        $itemId = 1;
        $fetchData = [];
        $storeId = 1;

        $readMock = $this->getAdapterMock($fetchData);

        $this->resourceModel->setConnection($readMock);

        $orderItemsCollectionMock = $this->prepareOrderItemCollectionMock();

        $this->orderItemCollection->expects($this->once())
            ->method('create')
            ->will($this->returnValue($orderItemsCollectionMock));

        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $this->productFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($productMock));

        $itemMockCanReturn = $this->prepareOrderItemMock($itemId, $storeId);

        $iterator = new \ArrayIterator([$itemMockCanReturn]);

        $orderItemsCollectionMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iterator));

        $this->rmaHelper->expects($this->at(0))
            ->method('canReturnProduct')
            ->with($this->equalTo($productMock), $this->equalTo($storeId))
            ->will($this->returnValue(false));

        $result = $this->resourceModel->getOrderItems($orderId);
        $this->assertEquals($orderItemsCollectionMock, $result);
    }

    /**
     * Get universal adapter mock with specified result for fetchPairs
     *
     * @param array $data
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAdapterMock($data)
    {
        $this->appResource->expects($this->any())
            ->method('getTableName')
            ->will($this->returnArgument(0));

        $selectMock = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->disableOriginalConstructor()
            ->getMock();
        $selectMock->expects($this->any())
            ->method('from')
            ->will($this->returnSelf());
        $selectMock->expects($this->any())
            ->method('joinInner')
            ->will($this->returnSelf());
        $selectMock->expects($this->any())
            ->method('where')
            ->will($this->returnSelf());

        $adapterMock = $this->getMockBuilder('Magento\Framework\DB\Adapter\Pdo\Mysql')
            ->disableOriginalConstructor()
            ->getMock();
        $adapterMock->expects($this->any())
            ->method('select')
            ->will($this->returnValue($selectMock));
        $adapterMock->expects($this->atLeastOnce())
            ->method('fetchPairs')
            ->will($this->returnValue($data));

        return $adapterMock;
    }

    /**
     * @param int $itemId
     * @param int $storeId
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function prepareOrderItemMock($itemId, $storeId)
    {
        $itemMockCanReturn = $this->getMockBuilder('Magento\Sales\Model\Order\Item')
            ->disableOriginalConstructor()
            ->setMethods(['getParentItemId', 'getId', '__wakeup', 'getStoreId'])
            ->getMock();
        $itemMockCanReturn->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($itemId));
        $itemMockCanReturn->expects($this->once())
            ->method('getStoreId')
            ->will($this->returnValue($storeId));
        return $itemMockCanReturn;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function prepareOrderItemCollectionMock()
    {
        $orderItemsCollectionMock = $this->getMockBuilder('Magento\Sales\Model\Resource\Order\Item\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $orderItemsCollectionMock->expects($this->once())
            ->method('addExpressionFieldToSelect')
            ->will($this->returnSelf());
        $orderItemsCollectionMock->expects($this->any())
            ->method('addFieldToFilter')
            ->will($this->returnSelf());
        $orderItemsCollectionMock->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1));
        $orderItemsCollectionMock->expects($this->any())
            ->method('removeItemByKey');
        return $orderItemsCollectionMock;
    }
}
