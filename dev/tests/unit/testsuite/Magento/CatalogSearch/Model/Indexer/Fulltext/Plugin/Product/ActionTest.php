<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Model\Indexer\Fulltext\Plugin\Product;

use Magento\CatalogSearch\Model\Indexer\Fulltext\Plugin\Product\Action;

class ActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Indexer\Model\IndexerInterface
     */
    protected $indexerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product\Action
     */
    protected $subjectMock;

    /**
     * @var \Magento\Indexer\Model\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerRegistryMock;

    /**
     * @var Action
     */
    protected $model;

    protected function setUp()
    {
        $this->subjectMock = $this->getMock('Magento\Catalog\Model\Product\Action', array(), array(), '', false);

        $this->indexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            array(),
            '',
            false,
            false,
            true,
            array('getId', 'getState', '__wakeup')
        );
        $this->indexerRegistryMock = $this->getMock('Magento\Indexer\Model\IndexerRegistry', ['get'], [], '', false);

        $this->model = new Action($this->indexerRegistryMock);
    }

    public function testAroundUpdateAttributesNonScheduled()
    {
        $this->indexerMock->expects($this->once())->method('isScheduled')->will($this->returnValue(false));
        $this->indexerMock->expects($this->once())->method('reindexList')->with(array(1, 2, 3));
        $this->prepareIndexer();

        $closureMock = function ($productIds, $attrData, $storeId) {
            $this->assertEquals(array(1, 2, 3), $productIds);
            $this->assertEquals(array(4, 5, 6), $attrData);
            $this->assertEquals(1, $storeId);
            return $this->subjectMock;
        };

        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundUpdateAttributes($this->subjectMock, $closureMock, array(1, 2, 3), array(4, 5, 6), 1)
        );
    }

    public function testAroundUpdateAttributesScheduled()
    {
        $this->indexerMock->expects($this->once())->method('isScheduled')->will($this->returnValue(true));
        $this->indexerMock->expects($this->never())->method('reindexList');
        $this->prepareIndexer();

        $closureMock = function ($productIds, $attrData, $storeId) {
            $this->assertEquals(array(1, 2, 3), $productIds);
            $this->assertEquals(array(4, 5, 6), $attrData);
            $this->assertEquals(1, $storeId);
            return $this->subjectMock;
        };

        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundUpdateAttributes($this->subjectMock, $closureMock, array(1, 2, 3), array(4, 5, 6), 1)
        );
    }

    public function testAroundUpdateWebsitesNonScheduled()
    {
        $this->indexerMock->expects($this->once())->method('isScheduled')->will($this->returnValue(false));
        $this->indexerMock->expects($this->once())->method('reindexList')->with(array(1, 2, 3));
        $this->prepareIndexer();

        $closureMock = function ($productIds, $websiteIds, $type) {
            $this->assertEquals(array(1, 2, 3), $productIds);
            $this->assertEquals(array(4, 5, 6), $websiteIds);
            $this->assertEquals('type', $type);
            return $this->subjectMock;
        };

        $this->model->aroundUpdateWebsites($this->subjectMock, $closureMock, array(1, 2, 3), array(4, 5, 6), 'type');
    }

    public function testAroundUpdateWebsitesScheduled()
    {
        $this->indexerMock->expects($this->once())->method('isScheduled')->will($this->returnValue(true));
        $this->indexerMock->expects($this->never())->method('reindexList');
        $this->prepareIndexer();

        $closureMock = function ($productIds, $websiteIds, $type) {
            $this->assertEquals(array(1, 2, 3), $productIds);
            $this->assertEquals(array(4, 5, 6), $websiteIds);
            $this->assertEquals('type', $type);
            return $this->subjectMock;
        };

        $this->model->aroundUpdateWebsites($this->subjectMock, $closureMock, array(1, 2, 3), array(4, 5, 6), 'type');
    }

    protected function prepareIndexer()
    {
        $this->indexerRegistryMock->expects($this->once())
            ->method('get')
            ->with(\Magento\CatalogSearch\Model\Indexer\Fulltext::INDEXER_ID)
            ->will($this->returnValue($this->indexerMock));
    }
}
