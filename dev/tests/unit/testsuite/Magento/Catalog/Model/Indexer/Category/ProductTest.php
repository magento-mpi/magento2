<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Product
     */
    protected $model;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Product\Action\FullFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fullMock;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Product\Action\RowsFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rowsMock;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerMock;

    protected function setUp()
    {
        $this->fullMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Category\Product\Action\FullFactory',
            array('create'), array(), '', false
        );

        $this->rowsMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Category\Product\Action\RowsFactory',
            array('create'), array(), '', false
        );

        $this->indexerMock = $this->getMockForAbstractClass(
            'Magento\Indexer\Model\IndexerInterface',
            array(), '', false, false, true, array('getId', 'load', 'isInvalid', 'isWorking', '__wakeup')
        );

        $this->model = new \Magento\Catalog\Model\Indexer\Category\Product(
            $this->fullMock, $this->rowsMock, $this->indexerMock
        );
    }

    public function testExecuteWithIndexerWorking()
    {
        $ids = array(1, 2, 3);

        $this->indexerMock->expects($this->once())
            ->method('load')
            ->with('catalog_category_product')
            ->will($this->returnSelf());
        $this->indexerMock->expects($this->once())
            ->method('isWorking')
            ->will($this->returnValue(true));

        $rowMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Category\Product\Action\Rows', array('execute'), array(), '', false
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
        $ids = array(1, 2, 3);

        $this->indexerMock->expects($this->once())
            ->method('load')
            ->with('catalog_category_product')
            ->will($this->returnSelf());
        $this->indexerMock->expects($this->once())
            ->method('isWorking')
            ->will($this->returnValue(false));

        $rowMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Category\Product\Action\Rows', array('execute'), array(), '', false
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
