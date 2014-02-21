<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Collection;


class AbstractCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Eav\Model\Entity\Collection\AbstractCollectionStub|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \Magento\Core\Model\EntityFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreEntityFactoryMock;
    /**
     * @var \Magento\Logger|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var \Magento\Data\Collection\Db\FetchStrategyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fetchStrategyMock;

    /**
     * @var \Magento\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreResourceMock;

    /**
     * @var \Magento\Eav\Model\EntityFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityFactoryMock;

    /**
     * @var \Magento\DB\Adapter\Pdo\Mysql|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    /**
     * @var \Magento\Eav\Model\Resource\Helper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceHelperMock;

    /**
     * @var \Magento\Validator\UniversalFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $validatorFactoryMock;

    /**
     * @var \Zend_Db_Select|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $selectMock;

    public function setUp()
    {
        $this->coreEntityFactoryMock = $this->getMock(
            'Magento\Core\Model\EntityFactory', array(), array(), '', false
        );
        $this->loggerMock = $this->getMock(
            'Magento\Logger', array(), array(), '', false
        );
        $this->fetchStrategyMock = $this->getMock(
            'Magento\Data\Collection\Db\FetchStrategyInterface', array(), array(), '', false
        );
        $this->eventManagerMock = $this->getMock(
            'Magento\Event\ManagerInterface', array(), array(), '', false
        );
        $this->configMock = $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false);
        $this->coreResourceMock = $this->getMock(
            'Magento\App\Resource', array('getConnection'), array(), '', false
        );
        $this->connectionMock = $this->getMock(
            'Magento\DB\Adapter\Pdo\Mysql', array(), array(), '', false
        );
        $this->resourceHelperMock = $this->getMock(
            'Magento\Eav\Model\Resource\Helper', array(), array(), '', false
        );
        $this->validatorFactoryMock = $this->getMock(
            'Magento\Validator\UniversalFactory', array(), array(), '', false
        );
        $this->entityFactoryMock = $this->getMock(
            'Magento\Eav\Model\EntityFactory', array(), array(), '', false
        );
        $this->selectMock = $this->getMock(
            'Zend_Db_Select', array(), array(), '', false
        );
        $this->coreEntityFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(array($this, 'getMagentoObject')));
        $this->connectionMock->expects($this->any())
            ->method('select')
            ->will($this->returnValue($this->selectMock));

        $this->coreResourceMock->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->connectionMock));
        $entityMock = $this->getMock(
            'Magento\Eav\Model\Entity\AbstractEntity', array(), array(), '', false
        );
        $entityMock->expects($this->once())
            ->method('getReadConnection')
            ->will($this->returnValue($this->connectionMock));
        $entityMock->expects($this->once())
            ->method('getDefaultAttributes')
            ->will($this->returnValue(array()));

        $this->validatorFactoryMock->expects($this->once())
            ->method('create')
            ->with('test_entity_model')// see \Magento\Eav\Model\Entity\Collection\AbstractCollectionStub
            ->will($this->returnValue($entityMock));
    }

    public function tearDown()
    {
        $this->model = null;
    }

    /**
     * @dataProvider getItemsDataProvider
     */
    public function testClear($values, $count)
    {
        $this->fetchStrategyMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue($values));

        $this->model = new \Magento\Eav\Model\Entity\Collection\AbstractCollectionStub(
            $this->coreEntityFactoryMock,
            $this->loggerMock,
            $this->fetchStrategyMock,
            $this->eventManagerMock,
            $this->configMock,
            $this->coreResourceMock,
            $this->entityFactoryMock,
            $this->resourceHelperMock,
            $this->validatorFactoryMock,
            null
        );
        $testId = array_pop($values)['id'];
        $this->assertCount($count, $this->model->getItems());
        $this->assertNotNull($this->model->getItemById($testId));
        $this->model->clear();
        $this->assertNull($this->model->getItemById($testId));
    }

    /**
     * @dataProvider getItemsDataProvider
     */
    public function testRemoveAllItems($values, $count)
    {
        $this->fetchStrategyMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue($values));

        $this->model = new \Magento\Eav\Model\Entity\Collection\AbstractCollectionStub(
            $this->coreEntityFactoryMock,
            $this->loggerMock,
            $this->fetchStrategyMock,
            $this->eventManagerMock,
            $this->configMock,
            $this->coreResourceMock,
            $this->entityFactoryMock,
            $this->resourceHelperMock,
            $this->validatorFactoryMock,
            null
        );
        $testId = array_pop($values)['id'];
        $this->assertCount($count, $this->model->getItems());
        $this->assertNotNull($this->model->getItemById($testId));
        $this->model->removeAllItems();
        $this->assertNull($this->model->getItemById($testId));
    }

    /**
     * @dataProvider getItemsDataProvider
     */
    public function testRemoveItemByKey($values, $count)
    {
        $this->fetchStrategyMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue($values));

        $this->model = new \Magento\Eav\Model\Entity\Collection\AbstractCollectionStub(
            $this->coreEntityFactoryMock,
            $this->loggerMock,
            $this->fetchStrategyMock,
            $this->eventManagerMock,
            $this->configMock,
            $this->coreResourceMock,
            $this->entityFactoryMock,
            $this->resourceHelperMock,
            $this->validatorFactoryMock,
            null
        );
        $testId = array_pop($values)['id'];
        $this->assertCount($count, $this->model->getItems());
        $this->assertNotNull($this->model->getItemById($testId));
        $this->model->removeItemByKey($testId);
        $this->assertCount($count - 1, $this->model->getItems());
        $this->assertNull($this->model->getItemById($testId));
    }

    public function getItemsDataProvider()
    {
        return array(
            array('values' => array(array('id' => 1)), 'count' => 1),
            array('values' => array(array('id' => 1), array('id' => 2)), 'count' => 2),
            array('values' => array(array('id' => 2), array('id' => 3)), 'count' => 2),
        );
    }

    public function getMagentoObject()
    {
        return new \Magento\Object();
    }
}
