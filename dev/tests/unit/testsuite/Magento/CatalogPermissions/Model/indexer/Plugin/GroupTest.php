<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin\Store;

class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Indexer\Model\IndexerInterface
     */
    protected $indexerMock;

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
        $this->model = new Group($this->indexerMock);
    }

    /**
     * @param array $valueMap
     * @dataProvider changedDataProvider
     */
    public function testAroundSave($valueMap)
    {
        $this->mockIndexerMethods();
        $groupMock = $this->getMock(
            'Magento\Core\Model\Store\Group', array('dataHasChangedFor', 'isObjectNew', '__wakeup'), array(), '', false
        );
        $groupMock->expects($this->exactly(2))
            ->method('dataHasChangedFor')
            ->will($this->returnValueMap($valueMap));
        $groupMock->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(false));

        $arguments = array($groupMock);
        $this->mockPluginProceed($arguments);
        $this->assertFalse($this->model->aroundSave($arguments, $this->pluginMock));
    }

    /**
     * @param array $valueMap
     * @dataProvider changedDataProvider
     */
    public function testAroundSaveNotNew($valueMap)
    {
        $groupMock = $this->getMock(
            'Magento\Core\Model\Store\Group', array('dataHasChangedFor', 'isObjectNew', '__wakeup'), array(), '', false
        );
        $groupMock->expects($this->exactly(2))
            ->method('dataHasChangedFor')
            ->will($this->returnValueMap($valueMap));
        $groupMock->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(true));

        $arguments = array($groupMock);
        $this->mockPluginProceed($arguments);
        $this->assertFalse($this->model->aroundSave($arguments, $this->pluginMock));
    }

    public function changedDataProvider()
    {
        return array(array(
            array(
                array('root_category_id', true),
                array('website_id', false),
            ),
            array(
                array('root_category_id', false),
                array('website_id', true),
            ),
        ));
    }

    public function testAroundSaveWithoutChanges()
    {
        $groupMock = $this->getMock(
            'Magento\Core\Model\Store\Group', array('dataHasChangedFor', 'isObjectNew', '__wakeup'), array(), '', false
        );
        $groupMock->expects($this->exactly(2))
            ->method('dataHasChangedFor')
            ->will($this->returnValueMap(array(
                array('root_category_id', false),
                array('website_id', false),
            )));
        $groupMock->expects($this->never())
            ->method('isObjectNew');

        $arguments = array($groupMock);
        $this->mockPluginProceed($arguments);
        $this->assertFalse($this->model->aroundSave($arguments, $this->pluginMock));

    }

    protected function mockIndexerMethods()
    {
        $this->indexerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('invalidate');
    }

    protected function mockPluginProceed($arguments, $returnValue = false)
    {
        $this->pluginMock->expects($this->once())
            ->method('proceed')
            ->with($arguments)
            ->will($this->returnValue($returnValue));
    }
}
