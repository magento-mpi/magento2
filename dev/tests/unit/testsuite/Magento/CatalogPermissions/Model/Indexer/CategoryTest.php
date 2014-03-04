<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\Model\Indexer;

class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Category
     */
    protected $model;

    /**
     * @var Category\Action\FullFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fullMock;

    /**
     * @var Category\Action\RowsFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rowsMock;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerMock;

    protected function setUp()
    {
        $this->fullMock = $this->getMock(
            'Magento\CatalogPermissions\Model\Indexer\Category\Action\FullFactory', ['create'], [], '', false
        );

        $this->rowsMock = $this->getMock(
            'Magento\CatalogPermissions\Model\Indexer\Category\Action\RowsFactory', ['create'], [], '', false
        );

        $methods = ['getId', 'load', 'isInvalid', 'isWorking', '__wakeup'];
        $this->indexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface', [], '', false, false, true, $methods
        );

        $this->model = new \Magento\CatalogPermissions\Model\Indexer\Category(
            $this->fullMock,
            $this->rowsMock,
            $this->indexerMock
        );
    }

    public function testExecuteWithIndexerWorking()
    {
        $ids = [1, 2, 3];

        $this->indexerMock->expects($this->once())
            ->method('load')
            ->with(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)
            ->will($this->returnSelf());
        $this->indexerMock->expects($this->once())
            ->method('isWorking')
            ->will($this->returnValue(true));

        $rowMock = $this->getMock(
            'Magento\CatalogPermissions\Model\Indexer\Category\Action\Rows', ['execute'], [], '', false
        );
        $rowMock->expects($this->at(0))
            ->method('execute')
            ->with($ids, true)
            ->will($this->returnSelf());
        $rowMock->expects($this->at(1))
            ->method('execute')
            ->with($ids, false)
            ->will($this->returnSelf());

        $this->rowsMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($rowMock));

        $this->model->execute($ids);
    }

    public function testExecuteWithIndexerNotWorking()
    {
        $ids = [1, 2, 3];

        $this->indexerMock->expects($this->once())
            ->method('load')
            ->with(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)
            ->will($this->returnSelf());
        $this->indexerMock->expects($this->once())
            ->method('isWorking')
            ->will($this->returnValue(false));

        $rowMock = $this->getMock(
            'Magento\CatalogPermissions\Model\Indexer\Category\Action\Rows', ['execute'], [], '', false
        );
        $rowMock->expects($this->once())
            ->method('execute')
            ->with($ids, false)
            ->will($this->returnSelf());

        $this->rowsMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($rowMock));

        $this->model->execute($ids);
    }
}
