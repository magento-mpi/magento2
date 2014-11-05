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
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\CatalogPermissions\App\ConfigInterface
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Store\Model\Resource\Group
     */
    protected $subjectMock;

    /**
     * @var \Magento\Indexer\Model\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerRegistryMock;

    /**
     * @var Group
     */
    protected $model;

    protected function setUp()
    {
        $this->subjectMock = $this->getMock('Magento\Store\Model\Resource\Group', array(), array(), '', false);
        $this->indexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            array(),
            '',
            false,
            false,
            true,
            array('getId', 'getState', '__wakeup')
        );
        $this->configMock = $this->getMockForAbstractClass(
            'Magento\CatalogPermissions\App\ConfigInterface',
            array(),
            '',
            false,
            false,
            true,
            array('isEnabled')
        );
        $this->configMock->expects($this->any())->method('isEnabled')->will($this->returnValue(true));
        $this->indexerRegistryMock = $this->getMock('Magento\Indexer\Model\IndexerRegistry', ['get'], [], '', false);
        $this->model = new Group($this->indexerRegistryMock, $this->configMock);
    }

    /**
     * @param array $valueMap
     * @dataProvider changedDataProvider
     */
    public function testAroundSave($valueMap)
    {
        $this->mockIndexerMethods();
        $groupMock = $this->getMock(
            'Magento\Store\Model\Group',
            array('dataHasChangedFor', 'isObjectNew', '__wakeup'),
            array(),
            '',
            false
        );
        $groupMock->expects($this->exactly(2))->method('dataHasChangedFor')->will($this->returnValueMap($valueMap));
        $groupMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(false));

        $closureMock = function () use ($groupMock) {
            return $this->subjectMock;
        };
        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundSave($this->subjectMock, $closureMock, $groupMock)
        );
    }

    /**
     * @param array $valueMap
     * @dataProvider changedDataProvider
     */
    public function testAroundSaveNotNew($valueMap)
    {
        $groupMock = $this->getMock(
            'Magento\Store\Model\Group',
            array('dataHasChangedFor', 'isObjectNew', '__wakeup'),
            array(),
            '',
            false
        );
        $groupMock->expects($this->exactly(2))->method('dataHasChangedFor')->will($this->returnValueMap($valueMap));
        $groupMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $closureMock = function () use ($groupMock) {
            return $this->subjectMock;
        };
        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundSave($this->subjectMock, $closureMock, $groupMock)
        );
    }

    public function changedDataProvider()
    {
        return array(
            array(
                array(array('root_category_id', true), array('website_id', false)),
                array(array('root_category_id', false), array('website_id', true))
            )
        );
    }

    public function testAroundSaveWithoutChanges()
    {
        $groupMock = $this->getMock(
            'Magento\Store\Model\Group',
            array('dataHasChangedFor', 'isObjectNew', '__wakeup'),
            array(),
            '',
            false
        );
        $groupMock->expects(
            $this->exactly(2)
        )->method(
            'dataHasChangedFor'
        )->will(
            $this->returnValueMap(array(array('root_category_id', false), array('website_id', false)))
        );
        $groupMock->expects($this->never())->method('isObjectNew');

        $closureMock = function () use ($groupMock) {
            return $this->subjectMock;
        };
        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundSave($this->subjectMock, $closureMock, $groupMock)
        );
    }

    protected function mockIndexerMethods()
    {
        $this->indexerMock->expects($this->once())->method('invalidate');
        $this->indexerRegistryMock->expects($this->once())
            ->method('get')
            ->with(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)
            ->will($this->returnValue($this->indexerMock));
    }
}
