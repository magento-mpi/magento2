<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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

    public function testGetItemsIdsByOrder()
    {
        $data = ['item' => 1];
        $this->appResource->expects($this->any())
            ->method('getTableName')
            ->will($this->returnArgument(0));

        $subSelectMock = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->disableOriginalConstructor()
            ->getMock();
        $subSelectMock->expects($this->once())
            ->method('from')
            ->with(['main' => 'magento_rma'], [])
            ->will($this->returnSelf());
        $subSelectMock->expects($this->any())
            ->method('where')
            ->will($this->returnSelf());

        $expression = new \Zend_Db_Expr('(qty_shipped - qty_requested)');

        $selectMock = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->disableOriginalConstructor()
            ->getMock();
        $selectMock->expects($this->once())
            ->method('from')
            ->with(
                ['item_entity' => 'magento_rma_item_entity'],
                ['item_entity.order_item_id', 'item_entity.order_item_id', 'can_return' => $expression]
            )
            ->will($this->returnSelf());
        $selectMock->expects($this->once())
            ->method('exists')
            ->with($subSelectMock, 'main.entity_id = item_entity.rma_entity_id')
            ->will($this->returnSelf());
        $selectMock->expects($this->once())
            ->method('joinInner')
            ->with(
                ['flat_item' => 'sales_flat_order_item'],
                'flat_item.item_id = item_entity.order_item_id'
            )
            ->will($this->returnSelf());

        $readMock = $this->getMockBuilder('Magento\Framework\DB\Adapter\Pdo\Mysql')
            ->disableOriginalConstructor()
            ->getMock();
        $readMock->expects($this->at(0))
            ->method('select')
            ->will($this->returnValue($subSelectMock));
        $readMock->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnArgument(0));
        $readMock->expects($this->at(3))
            ->method('select')
            ->will($this->returnValue($selectMock));
        $readMock->expects($this->once())
            ->method('fetchAll')
            ->with($selectMock)
            ->will($this->returnValue($data));

        $this->resourceModel->setConnection($readMock);
        $orderId = 1000001;
        $result = $this->resourceModel->getItemsIdsByOrder($orderId);
        $this->assertEquals($data, $result);
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

        $selectMock = $this->prepareSelectMock();

        $readMock = $this->prepareAdapterMock($selectMock);

        $this->resourceModel->setConnection($readMock);

        $expression = new \Zend_Db_Expr('(qty_shipped - qty_returned)');

        $orderItemsCollectionMock = $this->prepareOrderItemCollectionMock($expression);

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
        $fetchData = [['order_item_id' => $itemId, 'can_return' => true]];
        $storeId = 1;

        $selectMock = $this->prepareSelectMock();

        $readMock = $this->prepareAdapterMock($selectMock);
        $readMock->expects($this->at(6))
            ->method('fetchAll')
            ->with($selectMock)
            ->will($this->returnValue($fetchData));

        $this->resourceModel->setConnection($readMock);

        $expression = new \Zend_Db_Expr('(qty_shipped - qty_returned)');

        $orderItemsCollectionMock = $this->prepareOrderItemCollectionMock($expression);

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

    public function testGetOrderItemsCanReturnEmpty()
    {
        $orderId = 10000001;
        $itemId = 1;
        $fetchData = [];
        $storeId = 1;

        $selectMock = $this->prepareSelectMock();

        $readMock = $this->prepareAdapterMock($selectMock);
        $readMock->expects($this->at(6))
            ->method('fetchAll')
            ->with($selectMock)
            ->will($this->returnValue($fetchData));

        $this->resourceModel->setConnection($readMock);

        $expression = new \Zend_Db_Expr('(qty_shipped - qty_returned)');

        $orderItemsCollectionMock = $this->prepareOrderItemCollectionMock($expression);

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

        $selectMock = $this->prepareSelectMock();

        $readMock = $this->prepareAdapterMock($selectMock);
        $readMock->expects($this->at(6))
            ->method('fetchAll')
            ->with($selectMock)
            ->will($this->returnValue($fetchData));

        $this->resourceModel->setConnection($readMock);

        $expression = new \Zend_Db_Expr('(qty_shipped - qty_returned)');

        $orderItemsCollectionMock = $this->prepareOrderItemCollectionMock($expression);

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
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function prepareSelectMock()
    {
        $selectMock = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->disableOriginalConstructor()
            ->getMock();
        $selectMock->expects($this->any())
            ->method('from')
            ->will($this->returnSelf());
        $selectMock->expects($this->any())
            ->method('where')
            ->will($this->returnSelf());
        $selectMock->expects($this->any())
            ->method('exists')
            ->will($this->returnSelf());
        $selectMock->expects($this->any())
            ->method('joinInner')
            ->will($this->returnSelf());
        return $selectMock;
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
     * @param \Zend_Db_Expr $expression
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function prepareOrderItemCollectionMock(\Zend_Db_Expr $expression)
    {
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
            ->will($this->returnValue(1));
        $orderItemsCollectionMock->expects($this->any())
            ->method('removeItemByKey');
        return $orderItemsCollectionMock;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $selectMock
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function prepareAdapterMock(\PHPUnit_Framework_MockObject_MockObject $selectMock)
    {
        $readMock = $this->getMockBuilder('Magento\Framework\DB\Adapter\Pdo\Mysql')
            ->disableOriginalConstructor()
            ->getMock();
        $readMock->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnArgument(0));
        $readMock->expects($this->any())
            ->method('select')
            ->will($this->returnValue($selectMock));
        return $readMock;
    }
}
