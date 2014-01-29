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
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Indexer\Model\Indexer
     */
    protected $indexerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Indexer\Category\Flat\Config
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Resource\Category\Flat
     */
    protected $flatResourceMock;

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
        $this->indexerMock = $this->getMock(
            'Magento\Indexer\Model\Indexer', array('getId', 'getState'), array(), '', false
        );
        $this->configMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Category\Flat\Config', array('isFlatEnabled'), array(), '', false
        );
        $this->flatResourceMock = $this->getMock(
            'Magento\Catalog\Model\Resource\Category\Flat', array('deleteStores', '__wakeup'), array(), '', false
        );
        $this->model = new StoreGroup(
            $this->indexerMock,
            $this->configMock,
            $this->flatResourceMock
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
        $this->mockConfigFlatEnabledNeever();
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

    public function testAroundDelete()
    {
        $storeIds = array(1,2,3);
        $this->flatResourceMock->expects($this->once())
            ->method('deleteStores')
            ->with($storeIds);
        $groupMock = $this->getMock(
            'Magento\Core\Model\Store\Group', array('getStoreIds', '__wakeup'), array(), '', false
        );
        $groupMock->expects($this->once())
            ->method('getStoreIds')
            ->will($this->returnValue($storeIds));
        $arguments = array($groupMock);
        $this->mockPluginProceed($arguments);
        $this->assertFalse($this->model->aroundDelete($arguments, $this->pluginMock));
    }

    public function testAroundDeleteWithoutId()
    {
        $this->flatResourceMock->expects($this->never())
            ->method('deleteStores');
        $groupMock = $this->getMock(
            'Magento\Core\Model\Store\Group', array('getStoreIds', '__wakeup'), array(), '', false
        );
        $groupMock->expects($this->once())
            ->method('getStoreIds')
            ->will($this->returnValue(array()));
        $arguments = array($groupMock);
        $this->mockPluginProceed($arguments);
        $this->assertFalse($this->model->aroundDelete($arguments, $this->pluginMock));
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
            ->method('getState')
            ->will($this->returnValue($this->getStateMock()));
    }

    protected function mockConfigFlatEnabled()
    {
        $this->configMock->expects($this->once())
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

    protected function mockConfigFlatEnabledNeever()
    {
        $this->configMock->expects($this->never())
            ->method('isFlatEnabled');
    }
}