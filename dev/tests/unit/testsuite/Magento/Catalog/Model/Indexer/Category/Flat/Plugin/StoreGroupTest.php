<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class StoreGroupTest extends \PHPUnit_Framework_TestCase
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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupMock;

    protected function setUp()
    {
        $this->indexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            array(), '', false, false, true, array('getId', 'getState', '__wakeup')
        );
        $this->stateMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Category\Flat\State', array('isFlatEnabled'), array(), '', false
        );
        $this->subjectMock = $this->getMock('Magento\Core\Model\Resource\Store\Group', array(), array(), '', false);

        $this->groupMock =  $this->getMock(
            'Magento\Core\Model\Store\Group', array('dataHasChangedFor', 'isObjectNew', '__wakeup'), array(), '', false
        );
        $this->closureMock = function () {
            return false;
        };
        $this->model = new StoreGroup(
            $this->indexerMock,
            $this->stateMock
        );
    }

    public function testAroundSave()
    {
        $this->stateMock->expects($this->once())
            ->method('isFlatEnabled')
            ->will($this->returnValue(true));
        $this->indexerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('invalidate');
        $this->groupMock->expects($this->once())
            ->method('dataHasChangedFor')
            ->with('root_category_id')
            ->will($this->returnValue(true));
        $this->groupMock->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(false));
        $this->assertFalse($this->model->aroundSave($this->subjectMock, $this->closureMock, $this->groupMock));
    }

    public function testAroundSaveNotNew()
    {
        $this->stateMock->expects($this->never())
            ->method('isFlatEnabled');
        $this->groupMock = $this->getMock(
            'Magento\Core\Model\Store\Group', array('dataHasChangedFor', 'isObjectNew', '__wakeup'), array(), '', false
        );
        $this->groupMock->expects($this->once())
            ->method('dataHasChangedFor')
            ->with('root_category_id')
            ->will($this->returnValue(true));
        $this->groupMock->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(true));
        $this->assertFalse($this->model->aroundSave($this->subjectMock, $this->closureMock, $this->groupMock));
    }
}