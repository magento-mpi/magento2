<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Product\Plugin;

class StoreViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Indexer\Model\IndexerInterface
     */
    protected $indexerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|
     */
    protected $pluginMock;

    /**
     * @var StoreView
     */
    protected $model;

    /**
     * @var \Magento\Indexer\Model\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerRegistryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    protected function setUp()
    {
        $this->indexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            array(),
            '',
            false,
            false,
            true,
            array('getId', 'getState', '__wakeup')
        );
        $this->subject = $this->getMock('Magento\Store\Model\Resource\Group', array(), array(), '', false);
        $this->indexerRegistryMock = $this->getMock('Magento\Indexer\Model\IndexerRegistry', ['get'], [], '', false);

        $this->model = new StoreView($this->indexerRegistryMock);
    }

    public function testAroundSaveNewObject()
    {
        $this->mockIndexerMethods();
        $storeMock = $this->getMock(
            'Magento\Store\Model\Store',
            array('isObjectNew', 'dataHasChangedFor', '__wakeup'),
            array(),
            '',
            false
        );
        $storeMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $proceed = $this->mockPluginProceed();
        $this->assertFalse($this->model->aroundSave($this->subject, $proceed, $storeMock));
    }

    public function testAroundSaveHasChanged()
    {
        $this->mockIndexerMethods();
        $storeMock = $this->getMock(
            'Magento\Store\Model\Store',
            array('isObjectNew', 'dataHasChangedFor', '__wakeup'),
            array(),
            '',
            false
        );
        $storeMock->expects(
            $this->once()
        )->method(
            'dataHasChangedFor'
        )->with(
            'group_id'
        )->will(
            $this->returnValue(true)
        );
        $proceed = $this->mockPluginProceed();
        $this->assertFalse($this->model->aroundSave($this->subject, $proceed, $storeMock));
    }

    public function testAroundSaveNoNeed()
    {
        $storeMock = $this->getMock(
            'Magento\Store\Model\Store',
            array('isObjectNew', 'dataHasChangedFor', '__wakeup'),
            array(),
            '',
            false
        );
        $storeMock->expects(
            $this->once()
        )->method(
            'dataHasChangedFor'
        )->with(
            'group_id'
        )->will(
            $this->returnValue(false)
        );
        $proceed = $this->mockPluginProceed();
        $this->assertFalse($this->model->aroundSave($this->subject, $proceed, $storeMock));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Indexer\Model\Indexer\State
     */
    protected function getStateMock()
    {
        $stateMock = $this->getMock(
            'Magento\Indexer\Model\Indexer\State',
            array('setStatus', 'save', '__wakeup'),
            array(),
            '',
            false
        );
        $stateMock->expects($this->once())->method('setStatus')->with('invalid')->will($this->returnSelf());
        $stateMock->expects($this->once())->method('save')->will($this->returnSelf());

        return $stateMock;
    }

    protected function mockIndexerMethods()
    {
        $this->indexerMock->expects($this->once())->method('invalidate');
        $this->indexerRegistryMock->expects($this->once())
            ->method('get')
            ->with(\Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID)
            ->will($this->returnValue($this->indexerMock));
    }

    protected function mockPluginProceed($returnValue = false)
    {
        return function () use ($returnValue) {
            return $returnValue;
        };
    }
}
