<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category\Flat;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Indexer\Model\Indexer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerMock;

    protected function setUp()
    {
        $this->configMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Category\Flat\Config', array('isFlatEnabled'), array(), '', false
        );

        $this->indexerMock = $this->getMock(
            'Magento\Indexer\Model\Indexer',
            array('getId', 'load', 'getMode', 'reindexRow', 'reindexList', '__wakeup'),
            array(), '', false
        );

        $this->model = new \Magento\Catalog\Model\Indexer\Category\Flat\Processor(
            $this->configMock, $this->indexerMock
        );
    }

    /**
     * Mock getIndex() method
     */
    protected function mockGetIndexer()
    {
        $this->indexerMock->expects($this->at(0))
            ->method('getId')
            ->will($this->returnValue(null));
        $this->indexerMock->expects($this->once())
            ->method('load')
            ->with('catalog_category_flat')
            ->will($this->returnValue($this->indexerMock));
    }

    /**
     * Mock indexer's getMode() method
     *
     * @param string $mode
     */
    protected function mockIndexerGetMode($mode)
    {
        $this->indexerMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue($mode));
    }

    /**
     * Mock config isFlatEnabled() method
     *
     * @param bool $isEnabled
     */
    protected function mockConfigIsEnabled($isEnabled)
    {
        $this->configMock->expects($this->once())
            ->method('isFlatEnabled')
            ->will($this->returnValue($isEnabled));
    }

    public function testReindexRowWithFlatDisabled()
    {
        $this->mockConfigIsEnabled(false);

        $this->indexerMock->expects($this->never())
            ->method('getId');
        $this->indexerMock->expects($this->never())
            ->method('load');
        $this->indexerMock->expects($this->never())
            ->method('getMode');
        $this->indexerMock->expects($this->never())
            ->method('reindexRow');
        $this->model->reindexRow(1);
    }

    public function testReindexRowWithMviewOn()
    {
        $this->mockConfigIsEnabled(true);
        $this->mockGetIndexer();
        $this->mockIndexerGetMode('enabled');

        $this->indexerMock->expects($this->never())
            ->method('reindexRow');
        $this->model->reindexRow(1);
    }

    public function testReindexRowWithMviewOff()
    {
        $this->mockConfigIsEnabled(true);
        $this->mockGetIndexer();
        $this->mockIndexerGetMode('disabled');

        $this->indexerMock->expects($this->at(3))
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('reindexRow')
            ->with(1);
        $this->model->reindexRow(1);
    }

    public function testReindexListWithFlatDisabled()
    {
        $this->mockConfigIsEnabled(false);

        $this->indexerMock->expects($this->never())
            ->method('getId');
        $this->indexerMock->expects($this->never())
            ->method('load');
        $this->indexerMock->expects($this->never())
            ->method('getMode');
        $this->indexerMock->expects($this->never())
            ->method('reindexList');
        $this->model->reindexList(array(1, 2, 3));
    }

    public function testReindexListWithMviewOn()
    {
        $this->mockConfigIsEnabled(true);

        $this->mockGetIndexer();
        $this->mockIndexerGetMode('enabled');

        $this->indexerMock->expects($this->never())
            ->method('reindexList');
        $this->model->reindexList(array(1, 2, 3));
    }

    public function testReindexListWithMviewOff()
    {
        $this->mockConfigIsEnabled(true);

        $this->mockGetIndexer();
        $this->mockIndexerGetMode('disabled');

        $this->indexerMock->expects($this->at(3))
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('reindexList')
            ->with(array(1, 2, 3));

        $this->model->reindexList(array(1, 2, 3));
    }
}
