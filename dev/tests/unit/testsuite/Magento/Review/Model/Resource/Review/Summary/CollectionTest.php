<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Model\Resource\Review\Summary;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var \Magento\Framework\Data\Collection\Db\FetchStrategy\Query|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fetchStrategyMock;

    /**
     * @var \Magento\Core\Model\EntityFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityFactoryMock;

    /**
     * @var \Magento\Logger|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Zend_Db_Adapter_Pdo_Mysql|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapterMock;

    /**
     * @var \Magento\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $selectMock;

    protected function setUp()
    {
        $this->fetchStrategyMock = $this->getMock(
            'Magento\Framework\Data\Collection\Db\FetchStrategy\Query', array('fetchAll'), array(), '', false
        );
        $this->entityFactoryMock = $this->getMock(
            'Magento\Core\Model\EntityFactory', array('create'), array(), '', false
        );
        $this->loggerMock = $this->getMock('Magento\Logger', array('log'), array(), '', false);
        $this->resourceMock = $this->getMock(
            'Magento\Framework\App\Resource', array('getConnection', 'getTableName'), array(), '', false
        );
        $this->adapterMock = $this->getMock(
            'Zend_Db_Adapter_Pdo_Mysql', array('select', 'query'), array(), '', false
        );
        $this->selectMock = $this->getMock(
            'Magento\DB\Select', array('from'), array('adapter' => $this->adapterMock)
        );
        $this->adapterMock->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->selectMock));
        $this->resourceMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($this->adapterMock));
        $this->resourceMock->expects($this->once())
            ->method('getTableName')
            ->will($this->returnArgument(0));

        $this->collection = new Collection(
            $this->entityFactoryMock,
            $this->loggerMock,
            $this->fetchStrategyMock,
            $this->resourceMock
        );
    }

    public function testFetchItem()
    {
        $data = array(1 => 'test');
        $statementMock = $this->getMock('Zend_Db_Statement_Pdo', array('fetch'), array(), '', false);
        $statementMock->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue($data));

        $this->adapterMock->expects($this->once())
            ->method('query')
            ->with($this->selectMock, $this->anything())
            ->will($this->returnValue($statementMock));

        $objectMock = $this->getMock('Magento\Object', array('setData'), array());
        $objectMock->expects($this->once())
            ->method('setData')
            ->with($data);
        $this->entityFactoryMock->expects($this->once())
            ->method('create')
            ->with('Magento\Review\Model\Review\Summary')
            ->will($this->returnValue($objectMock));
        $item = $this->collection->fetchItem();

        $this->assertEquals($objectMock, $item);
        $this->assertEquals('primary_id', $item->getIdFieldName());
    }

    public function testLoad()
    {
        $data = array(10 => 'test');
        $this->fetchStrategyMock->expects($this->once())
            ->method('fetchAll')
            ->with($this->selectMock, array())
            ->will($this->returnValue(array($data)));

        $objectMock = $this->getMock('Magento\Object', array('addData'), array());
        $objectMock->expects($this->once())
            ->method('addData')
            ->with($data);
        $this->entityFactoryMock->expects($this->once())
            ->method('create')
            ->with('Magento\Review\Model\Review\Summary')
            ->will($this->returnValue($objectMock));

        $this->collection->load();
    }
}