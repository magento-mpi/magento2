<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\Model;

class IndexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Indexer\Model\Indexer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \Magento\Indexer\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Indexer\Model\ActionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionFactoryMock;

    /**
     * @var \Magento\Mview\ViewFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewFactoryMock;

    /**
     * @var \Magento\Indexer\Model\Indexer\StateFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateFactoryMock;

    /**
     * @var \Magento\Indexer\Model\Indexer\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexFactoryMock;

    protected function setUp()
    {
        $this->configMock = $this->getMock('Magento\Indexer\Model\Config', array('get'), array(), '', false);
        $this->actionFactoryMock = $this->getMock(
            'Magento\Indexer\Model\ActionFactory', array('create'), array(), '', false
        );
        $this->viewFactoryMock = $this->getMock('Magento\Mview\ViewFactory', array('create'), array(), '', false);
        $this->stateFactoryMock = $this->getMock(
            'Magento\Indexer\Model\Indexer\StateFactory', array('create'), array(), '', false
        );
        $this->indexFactoryMock = $this->getMock(
            'Magento\Indexer\Model\Indexer\CollectionFactory', array('create'), array(), '', false
        );
        $this->model = new Indexer(
            $this->configMock,
            $this->actionFactoryMock,
            $this->viewFactoryMock,
            $this->stateFactoryMock,
            $this->indexFactoryMock
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage indexer_id indexer does not exist.
     */
    public function testLoadWithException()
    {
        $indexId = 'indexer_id';
        $this->configMock->expects($this->once())
            ->method('get')
            ->with($indexId)
            ->will($this->returnValue($this->getIndexerData()));
        $this->model->load($indexId);
    }

    public function testGetView()
    {
        $indexId = 'indexer_internal_name';
        $view = $this->getMock('Magento\Mview\View', array('load'), array(), '', false);
        $view->expects($this->once())
            ->method('load')
            ->with('view_test')
            ->will($this->returnSelf());
        $this->viewFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($view));
        $this->configMock->expects($this->once())
            ->method('get')
            ->with($indexId)
            ->will($this->returnValue($this->getIndexerData()));
        $this->model->load($indexId);

        $this->assertEquals($view, $this->model->getView());
    }

    public function testGetState()
    {
        $indexId = 'indexer_internal_name';
        $stateMock = $this->getMock(
            '\Magento\Indexer\Model\Indexer\State',
            array('load', 'getId', 'setIndexerId', '__wakeup'),
            array(),
            '',
            false
        );
        $stateMock->expects($this->once())
            ->method('load')
            ->with($indexId, 'indexer_id')
            ->will($this->returnSelf());
        $stateMock->expects($this->never())
            ->method('setIndexerId');
        $stateMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->stateFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($stateMock));
        $this->configMock->expects($this->once())
            ->method('get')
            ->with($indexId)
            ->will($this->returnValue($this->getIndexerData()));
        $this->model->load($indexId);

        $this->assertInstanceOf('\Magento\Indexer\Model\Indexer\State', $this->model->getState());
    }

    /**
     * @param string $mode
     * @param string $status
     * @param bool $statusCall
     * @param bool $stateCall
     * @param string $result
     * @dataProvider indexerStatusProvider
     */
    public function testGetStatus($mode, $status, $statusCall, $stateCall, $result)
    {
        $indexId = 'indexer_internal_name';
        $view = $this->getMock(
            'Magento\Mview\View',
            array('load', 'getMode', 'getStatus'),
            array(),
            '',
            false
        );
        $view->expects($this->once())
            ->method('load')
            ->with('view_test')
            ->will($this->returnSelf());
        $view->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue($mode));
        $view->expects($statusCall ? $this->once() : $this->never())
            ->method('getStatus')
            ->will($this->returnValue($status));
        $this->viewFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($view));

        $stateMock = $this->getMock(
            '\Magento\Indexer\Model\Indexer\State',
            array('load', 'getId', 'setIndexerId', '__wakeup', 'getStatus'),
            array(),
            '',
            false
        );
        if ($stateCall) {
            $stateMock->expects($this->once())
                ->method('load')
                ->with($indexId, 'indexer_id')
                ->will($this->returnSelf());
            $stateMock->expects($this->never())
                ->method('setIndexerId');
            $stateMock->expects($this->once())
                ->method('getId')
                ->will($this->returnValue(1));
            $stateMock->expects($this->once())
                ->method('getStatus')
                ->will($this->returnValue($status));
            $this->stateFactoryMock->expects($this->once())
                ->method('create')
                ->will($this->returnValue($stateMock));
        }

        $this->configMock->expects($this->once())
            ->method('get')
            ->with($indexId)
            ->will($this->returnValue($this->getIndexerData()));
        $this->model->load($indexId);

        $this->assertEquals($result, $this->model->getStatus());
    }

    /**
     * @return array
     */
    public function indexerStatusProvider()
    {
        return array(
            'enabled_working' => array('enabled', 'working', true, false, 'working'),
            'enabled_idle'  => array('enabled', 'idle', true, true, 'idle'),
            'disabled_working' => array('disabled', 'working', false, true, 'working'),
            'disabled_idle' => array('disabled', 'idle', false, true, 'idle'),
        );
    }

    public function testGetUpdated()
    {
        $this->assertFalse(false);
    }

    protected function getIndexerData()
    {
        return array(
            'indexer_id' => 'indexer_internal_name',
            'view_id' => 'view_test',
            'class' => 'Some\Class\Name',
            'title' => 'Indexer public name',
            'description' => 'Indexer public description',
        );
    }
}
