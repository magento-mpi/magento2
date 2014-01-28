<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category\Flat;

class CategoryPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\Plugin\Category|\PHPUnit_Framework_MockObject_MockObject
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

    /**
     * @var \Magento\Code\Plugin\InvocationChain|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $chainMock;

    /**
     * @var \Magento\Catalog\Model\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryMock;

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

        $this->categoryMock = $this->getMock(
            'Magento\Catalog\Model\Category', array('getId', 'getAffectedCategoryIds', '__wakeup'), array(), '', false
        );

        $this->chainMock = $this->getMock(
            'Magento\Code\Plugin\InvocationChain', array('proceed'), array(), '', false
        );
        $this->chainMock->expects($this->once())
            ->method('proceed')
            ->with(array())
            ->will($this->returnValue($this->categoryMock));

        $this->model = new \Magento\Catalog\Model\Indexer\Category\Flat\Plugin\Category(
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

    public function testAroundSaveWithFlatDisabled()
    {
        $this->mockConfigIsEnabled(false);

        $this->categoryMock->expects($this->never())
            ->method('getId');
        $this->indexerMock->expects($this->never())
            ->method('getId');
        $this->indexerMock->expects($this->never())
            ->method('load');
        $this->indexerMock->expects($this->never())
            ->method('getMode');
        $this->indexerMock->expects($this->never())
            ->method('reindexRow');
        $this->model->aroundSave(array(), $this->chainMock);
    }

    public function testAroundSaveWithMviewOn()
    {
        $this->mockConfigIsEnabled(true);
        $this->mockGetIndexer();
        $this->mockIndexerGetMode('enabled');

        $this->categoryMock->expects($this->never())
            ->method('getId');
        $this->indexerMock->expects($this->never())
            ->method('reindexRow');
        $this->model->aroundSave(array(), $this->chainMock);
    }

    public function testAroundSaveWithMviewOff()
    {
        $this->mockConfigIsEnabled(true);
        $this->mockGetIndexer();
        $this->mockIndexerGetMode('disabled');

        $this->categoryMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->at(3))
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('reindexRow')
            ->with(1);
        $this->model->aroundSave(array(), $this->chainMock);
    }

    public function testAroundMoveWithFlatDisabled()
    {
        $this->mockConfigIsEnabled(false);

        $this->categoryMock->expects($this->never())
            ->method('getAffectedCategoryIds');
        $this->indexerMock->expects($this->never())
            ->method('getId');
        $this->indexerMock->expects($this->never())
            ->method('load');
        $this->indexerMock->expects($this->never())
            ->method('getMode');
        $this->indexerMock->expects($this->never())
            ->method('reindexList');
        $this->model->aroundMove(array(), $this->chainMock);
    }

    public function testAroundMoveWithoutAffectedIds()
    {
        $this->mockConfigIsEnabled(true);

        $this->categoryMock->expects($this->once())
            ->method('getAffectedCategoryIds')
            ->will($this->returnValue(null));
        $this->indexerMock->expects($this->never())
            ->method('getId');
        $this->indexerMock->expects($this->never())
            ->method('load');
        $this->indexerMock->expects($this->never())
            ->method('getMode');
        $this->indexerMock->expects($this->never())
            ->method('reindexList');
        $this->model->aroundMove(array(), $this->chainMock);
    }

    public function testAroundMoveWithMviewOn()
    {
        $this->mockConfigIsEnabled(true);

        $this->categoryMock->expects($this->once())
            ->method('getAffectedCategoryIds')
            ->will($this->returnValue(array(1)));

        $this->mockGetIndexer();
        $this->mockIndexerGetMode('enabled');

        $this->indexerMock->expects($this->never())
            ->method('reindexList');
        $this->model->aroundMove(array(), $this->chainMock);
    }

    public function testAroundMoveWithMviewOff()
    {
        $this->mockConfigIsEnabled(true);

        $this->categoryMock->expects($this->once())
            ->method('getAffectedCategoryIds')
            ->will($this->returnValue(array(1, 2)));

        $this->mockGetIndexer();
        $this->mockIndexerGetMode('disabled');

        $this->indexerMock->expects($this->at(3))
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('reindexList')
            ->with(array(1, 2));
        $this->model->aroundMove(array(), $this->chainMock);
    }
}
