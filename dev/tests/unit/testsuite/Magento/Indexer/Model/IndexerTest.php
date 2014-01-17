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
        $this->model = new Indexer(
            $this->configMock,
            $this->actionFactoryMock,
            $this->viewFactoryMock,
            $this->stateFactoryMock
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage indexer_id view does not exist.
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
        $view = new \stdClass();
        $this->viewFactoryMock->expects($this->once())
            ->method('create')
            ->with(array('viewId' => 'view_test'))
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

    protected function getIndexerData()
    {
        return array(
            'indexer_id' => 'indexer_internal_name',
            'view_id' => 'view_test',
            'class' => 'Some\Class\Name',
            'title' => 'Indexer public name',
            'title_translate' => true,
            'description' => 'Indexer public description',
            'description_translate' => true,
        );
    }
}
