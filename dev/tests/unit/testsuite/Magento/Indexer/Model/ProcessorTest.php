<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\Model;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Indexer\Model\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \Magento\Indexer\Model\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Indexer\Model\IndexerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerFactoryMock;

    /**
     * @var \Magento\Indexer\Model\Indexer\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexersFactoryMock;

    /**
     * @var \Magento\Mview\ProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewProcessorMock;

    protected function setUp()
    {
        $this->configMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\ConfigInterface', array(), '', false, false, true, array('getIndexers')
        );
        $this->indexerFactoryMock = $this->getMock(
            'Magento\Indexer\Model\IndexerFactory', array('create'), array(), '', false
        );
        $this->indexersFactoryMock = $this->getMock(
            'Magento\Indexer\Model\Indexer\CollectionFactory', array('create'), array(), '', false
        );
        $this->viewProcessorMock = $this->getMockForAbstractClass(
            'Magento\Mview\ProcessorInterface', array(), '', false
        );
        $this->model = new \Magento\Indexer\Model\Processor(
            $this->configMock,
            $this->indexerFactoryMock,
            $this->indexersFactoryMock,
            $this->viewProcessorMock
        );
    }

    public function testReindexAllInvalid()
    {
        $indexers = array(
            'indexer1',
            'indexer2',
        );

        $this->configMock->expects($this->once())
            ->method('getIndexers')
            ->will($this->returnValue($indexers));

        $state1Mock = $this->getMock('Magento\Indexer\Model\Indexer\State',
            array('getStatus', '__wakeup'), array(), '', false);
        $state1Mock->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue(Indexer\State::STATUS_INVALID));
        $indexer1Mock = $this->getMock('Magento\Indexer\Model\Indexer',
            array('load', 'getState', 'reindexAll'), array(), '', false);
        $indexer1Mock->expects($this->once())
            ->method('getState')
            ->will($this->returnValue($state1Mock));
        $indexer1Mock->expects($this->once())
            ->method('reindexAll');

        $state2Mock = $this->getMock('Magento\Indexer\Model\Indexer\State',
            array('getStatus', '__wakeup'), array(), '', false);
        $state2Mock->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue(Indexer\State::STATUS_VALID));
        $indexer2Mock = $this->getMock('Magento\Indexer\Model\Indexer',
            array('load', 'getState', 'reindexAll'), array(), '', false);
        $indexer2Mock->expects($this->never())
            ->method('reindexAll');
        $indexer2Mock->expects($this->once())
            ->method('getState')
            ->will($this->returnValue($state2Mock));

        $this->indexerFactoryMock->expects($this->at(0))
            ->method('create')
            ->will($this->returnValue($indexer1Mock));
        $this->indexerFactoryMock->expects($this->at(1))
            ->method('create')
            ->will($this->returnValue($indexer2Mock));

        $this->model->reindexAllInvalid();
    }

    public function testReindexAll()
    {
        $indexerMock = $this->getMock('Magento\Indexer\Model\Indexer', array(), array(), '', false);
        $indexerMock->expects($this->exactly(2))
            ->method('reindexAll');
        $indexers = array(
            $indexerMock,
            $indexerMock,
        );

        $indexersMock = $this->getMock('Magento\Indexer\Model\Indexer\Collection', array(), array(), '', false);
        $this->indexersFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($indexersMock));
        $indexersMock->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue($indexers));

        $this->model->reindexAll();
    }
}
