<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Product\Option;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var \Magento\Framework\Logger|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var \Magento\Core\Model\EntityFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityFactoryMock;

    /**
     * @var \Magento\Framework\Data\Collection\Db\FetchStrategyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fetchStrategyMock;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * @var Value\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionsFactoryMock;

    /**
     * @var \Magento\Framework\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Option|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapterMock;

    /**
     * @var \Zend_Db_Select|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $selectMock;

    protected function setUp()
    {
        $this->entityFactoryMock = $this->getMock(
            'Magento\Core\Model\EntityFactory', array('create'), array(), '', false
        );
        $this->loggerMock = $this->getMock('Magento\Framework\Logger', array('log'), array(), '', false);
        $this->fetchStrategyMock = $this->getMock(
            'Magento\Framework\Data\Collection\Db\FetchStrategy\Query', array('fetchAll'), array(), '', false
        );
        $this->eventManagerMock = $this->getMock('Magento\Framework\Event\Manager', array(), array(), '', false);
        $this->optionsFactoryMock = $this->getMock(
            'Magento\Catalog\Model\Resource\Product\Option\Value\CollectionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', array(), array(), '', false);
        $this->resourceMock = $this->getMock(
            'Magento\Catalog\Model\Resource\Product\Option',
            array('getReadConnection', '__wakeup', 'getMainTable', 'getTable'),
            array(),
            '',
            false
        );
        $this->selectMock = $this->getMock('Zend_Db_Select', array('from', 'reset'), array(), '', false);
        $this->adapterMock =
            $this->getMock('Magento\Framework\DB\Adapter\Pdo\Mysql', array('select'), array(), '', false);
        $this->adapterMock->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->selectMock));
        $this->resourceMock->expects($this->once())
            ->method('getReadConnection')
            ->will($this->returnValue($this->adapterMock));
        $this->resourceMock->expects($this->once())
            ->method('getMainTable')
            ->will($this->returnValue('test_main_table'));
        $this->resourceMock->expects($this->once())
            ->method('getTable')
            ->with('test_main_table')
            ->will($this->returnValue('test_main_table'));

        $this->collection = new Collection(
            $this->entityFactoryMock,
            $this->loggerMock,
            $this->fetchStrategyMock,
            $this->eventManagerMock,
            $this->optionsFactoryMock,
            $this->storeManagerMock,
            null,
            $this->resourceMock
        );
    }

    public function testReset()
    {
        $this->collection->reset();
    }
}