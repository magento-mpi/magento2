<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Indexer\Fulltext\Plugin\Store;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Indexer\Model\IndexerInterface
     */
    protected $indexerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Store\Model\Resource\Store
     */
    protected $subjectMock;

    /**
     * @var \Magento\Indexer\Model\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerRegistryMock;

    /**
     * @var View
     */
    protected $model;

    protected function setUp()
    {
        $this->subjectMock = $this->getMock('Magento\Store\Model\Resource\Store', [], [], '', false);
        $this->indexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            [],
            '',
            false,
            false,
            true,
            ['getId', 'getState', '__wakeup']
        );
        $this->indexerRegistryMock = $this->getMock('Magento\Indexer\Model\IndexerRegistry', ['get'], [], '', false);
        $this->model = new View($this->indexerRegistryMock);
    }

    /**
     * @param bool $isObjectNew
     * @param int $invalidateCounter
     * @return void
     * @dataProvider aroundSaveDataProvider
     */
    public function testAroundSave($isObjectNew, $invalidateCounter)
    {
        $viewMock = $this->getMock(
            'Magento\Store\Model\Store',
            ['dataHasChangedFor', 'isObjectNew', '__wakeup'],
            [],
            '',
            false
        );
        $viewMock->expects($this->once())->method('isObjectNew')->will($this->returnValue($isObjectNew));

        $closureMock = function (\Magento\Store\Model\Store $object) use ($viewMock) {
            $this->assertEquals($object, $viewMock);
            return $this->subjectMock;
        };

        $this->indexerMock->expects($this->exactly($invalidateCounter))->method('invalidate');
        $this->prepareIndexer($invalidateCounter);

        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundSave($this->subjectMock, $closureMock, $viewMock)
        );
    }

    /**
     * @return array
     */
    public function aroundSaveDataProvider()
    {
        return [
            [false, 0],
            [true, 1],
        ];
    }

    /**
     * @return void
     */
    public function testAfterDelete()
    {
        $this->indexerMock->expects($this->once())->method('invalidate');
        $this->prepareIndexer(1);

        $this->assertEquals(
            $this->subjectMock,
            $this->model->afterDelete($this->subjectMock, $this->subjectMock)
        );
    }

    /**
     * @param int $invalidateCounter
     */
    protected function prepareIndexer($invalidateCounter)
    {
        $this->indexerRegistryMock->expects($this->exactly($invalidateCounter))
            ->method('get')
            ->with(\Magento\CatalogSearch\Model\Indexer\Fulltext::INDEXER_ID)
            ->will($this->returnValue($this->indexerMock));
    }
}
