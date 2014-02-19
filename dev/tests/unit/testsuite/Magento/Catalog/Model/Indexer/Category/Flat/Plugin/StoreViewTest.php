<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class StoreViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Indexer\Model\IndexerInterface
     */
    protected $indexerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $stateMock;

    /**
     * @var StoreView
     */
    protected $model;

    /**
     * @var \Closure
     */
    protected $closureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->indexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            array(), '', false, false, true, array('getId', 'getState', '__wakeup')
        );
        $this->stateMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Category\Flat\State', array('isFlatEnabled'), array(), '', false
        );
        $this->closureMock = function () {
            return false;
        };
        $this->subjectMock = $this->getMock('Magento\Core\Model\Resource\Store', array(), array(), '', false);
        $this->model = new StoreView(
            $this->indexerMock,
            $this->stateMock
        );
    }

    public function testAroundSaveNewObject()
    {
        $this->mockConfigFlatEnabled();
        $this->mockIndexerMethods();
        $storeMock = $this->getMock(
            'Magento\Core\Model\Store', array('isObjectNew', 'dataHasChangedFor', '__wakeup'), array(), '', false
        );
        $storeMock->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(true));
        $this->assertFalse($this->model->aroundSave($this->subjectMock, $this->closureMock, $storeMock));
    }

    public function testAroundSaveHasChanged()
    {
        $storeMock = $this->getMock(
            'Magento\Core\Model\Store', array('isObjectNew', 'dataHasChangedFor', '__wakeup'), array(), '', false
        );
        $storeMock->expects($this->once())
            ->method('dataHasChangedFor')
            ->with('group_id')
            ->will($this->returnValue(true));
        $this->assertFalse($this->model->aroundSave($this->subjectMock, $this->closureMock, $storeMock));
    }

    public function testAroundSaveNoNeed()
    {
        $this->mockConfigFlatEnabledNeever();
        $storeMock = $this->getMock(
            'Magento\Core\Model\Store', array('isObjectNew', 'dataHasChangedFor', '__wakeup'), array(), '', false
        );
        $storeMock->expects($this->once())
            ->method('dataHasChangedFor')
            ->with('group_id')
            ->will($this->returnValue(false));
        $this->assertFalse($this->model->aroundSave($this->subjectMock, $this->closureMock, $storeMock));
    }

    protected function mockIndexerMethods()
    {
        $this->indexerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('invalidate');
    }

    protected function mockConfigFlatEnabled()
    {
        $this->stateMock->expects($this->once())
            ->method('isFlatEnabled')
            ->will($this->returnValue(true));
    }

    protected function mockConfigFlatEnabledNeever()
    {
        $this->stateMock->expects($this->never())
            ->method('isFlatEnabled');
    }
}