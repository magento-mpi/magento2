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
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Code\Plugin\InvocationChain
     */
    protected $pluginMock;

    /**
     * @var StoreView
     */
    protected $model;

    protected function setUp()
    {
        $this->pluginMock = $this->getMock(
            'Magento\Code\Plugin\InvocationChain', array('proceed'), array(), '', false
        );
        $this->indexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            array(), '', false, false, true, array('getId', 'getState', '__wakeup')
        );
        $this->stateMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Category\Flat\State', array('isFlatEnabled'), array(), '', false
        );
        $this->model = new StoreGroup(
            $this->indexerMock,
            $this->stateMock,
            'catalog_category_flat'
        );
    }

    public function testAroundSave()
    {
        $this->mockConfigFlatEnabled();
        $this->mockIndexerMethods();
        $groupMock = $this->getMock(
            'Magento\Core\Model\Store\Group', array('dataHasChangedFor', 'isObjectNew', '__wakeup'), array(), '', false
        );
        $groupMock->expects($this->once())
            ->method('dataHasChangedFor')
            ->with('root_category_id')
            ->will($this->returnValue(true));
        $groupMock->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(false));

        $arguments = array($groupMock);
        $this->mockPluginProceed($arguments);
        $this->assertFalse($this->model->aroundSave($arguments, $this->pluginMock));
    }

    public function testAroundSaveNotNew()
    {
        $this->mockConfigFlatEnabledNever();
        $groupMock = $this->getMock(
            'Magento\Core\Model\Store\Group', array('dataHasChangedFor', 'isObjectNew', '__wakeup'), array(), '', false
        );
        $groupMock->expects($this->once())
            ->method('dataHasChangedFor')
            ->with('root_category_id')
            ->will($this->returnValue(true));
        $groupMock->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(true));

        $arguments = array($groupMock);
        $this->mockPluginProceed($arguments);
        $this->assertFalse($this->model->aroundSave($arguments, $this->pluginMock));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Indexer\Model\Indexer\State
     */
    protected function getStateMock()
    {
        $stateMock = $this->getMock(
            'Magento\Indexer\Model\Indexer\State', array('setStatus', 'save', '__wakeup'), array(), '', false
        );
        $stateMock->expects($this->once())
            ->method('setStatus')
            ->with('invalid')
            ->will($this->returnSelf());
        $stateMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());

        return $stateMock;
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

    protected function mockPluginProceed($arguments, $returnValue = false)
    {
        $this->pluginMock->expects($this->once())
            ->method('proceed')
            ->with($arguments)
            ->will($this->returnValue($returnValue));
    }

    protected function mockConfigFlatEnabledNever()
    {
        $this->stateMock->expects($this->never())
            ->method('isFlatEnabled');
    }
}