<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin\Product;

class ActionTest extends \PHPUnit_Framework_TestCase
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
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product\Action
     */
    protected $subjectMock;

    /**
     * @var Action
     */
    protected $model;

    protected function setUp()
    {
        $this->subjectMock = $this->getMock('Magento\Catalog\Model\Product\Action', array(), array(), '', false);

        $this->indexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            array(), '', false, false, true, array('getId', 'getState', '__wakeup')
        );

        $this->configMock = $this->getMockForAbstractClass(
            'Magento\CatalogPermissions\App\ConfigInterface',
            array(), '', false, false, true, array('isEnabled')
        );
        $this->configMock->expects($this->any())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $this->model = new Action($this->indexerMock, $this->configMock);
    }

    public function testAroundUpdateAttributesNonScheduled()
    {
        $this->indexerMock->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('isScheduled')
            ->will($this->returnValue(false));
        $this->indexerMock->expects($this->once())
            ->method('reindexList')
            ->with([1, 2, 3]);

        $closureMock = function ($productIds, $attrData, $storeId) {
            $this->assertEquals([1, 2, 3], $productIds);
            $this->assertEquals([4, 5, 6], $attrData);
            $this->assertEquals(1, $storeId);
            return $this->subjectMock;
        };

        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundUpdateAttributes($this->subjectMock, $closureMock, [1, 2, 3], [4, 5, 6], 1)
        );
    }

    public function testAroundUpdateAttributesScheduled()
    {
        $this->indexerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('isScheduled')
            ->will($this->returnValue(true));
        $this->indexerMock->expects($this->never())
            ->method('reindexList');

        $closureMock = function ($productIds, $attrData, $storeId) {
            $this->assertEquals([1, 2, 3], $productIds);
            $this->assertEquals([4, 5, 6], $attrData);
            $this->assertEquals(1, $storeId);
            return $this->subjectMock;
        };

        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundUpdateAttributes($this->subjectMock, $closureMock, [1, 2, 3], [4, 5, 6], 1)
        );
    }

    public function testAroundUpdateWebsitesNonScheduled()
    {
        $this->indexerMock->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('isScheduled')
            ->will($this->returnValue(false));
        $this->indexerMock->expects($this->once())
            ->method('reindexList')
            ->with([1, 2, 3]);

        $closureMock = function ($productIds, $websiteIds, $type) {
            $this->assertEquals([1, 2, 3], $productIds);
            $this->assertEquals([4, 5, 6], $websiteIds);
            $this->assertEquals('type', $type);
            return $this->subjectMock;
        };

        $this->model->aroundUpdateWebsites($this->subjectMock, $closureMock, [1, 2, 3], [4, 5, 6], 'type');
    }

    public function testAroundUpdateWebsitesScheduled()
    {
        $this->indexerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('isScheduled')
            ->will($this->returnValue(true));
        $this->indexerMock->expects($this->never())
            ->method('reindexList');

        $closureMock = function ($productIds, $websiteIds, $type) {
            $this->assertEquals([1, 2, 3], $productIds);
            $this->assertEquals([4, 5, 6], $websiteIds);
            $this->assertEquals('type', $type);
            return $this->subjectMock;
        };

        $this->model->aroundUpdateWebsites($this->subjectMock, $closureMock, [1, 2, 3], [4, 5, 6], 'type');
    }
}
