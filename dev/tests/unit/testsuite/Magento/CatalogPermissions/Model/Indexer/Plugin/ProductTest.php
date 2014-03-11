<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

use \Magento\CatalogPermissions\Model\Indexer\Plugin\Product;

class ProductTest extends \PHPUnit_Framework_TestCase
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
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product
     */
    protected $subjectMock;

    /**
     * @var Product
     */
    protected $model;

    protected function setUp()
    {
        $this->subjectMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);

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

        $this->model = new Product($this->indexerMock, $this->configMock);
    }

    public function testAfterSaveNonScheduled()
    {
        $this->indexerMock->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('isScheduled')
            ->will($this->returnValue(false));
        $this->indexerMock->expects($this->once())
            ->method('reindexList')
            ->with([1]);

        $this->subjectMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->assertEquals($this->subjectMock, $this->model->afterSave($this->subjectMock));
    }

    public function testAfterSaveScheduled()
    {
        $this->indexerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('isScheduled')
            ->will($this->returnValue(true));
        $this->indexerMock->expects($this->never())
            ->method('reindexList');

        $this->subjectMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->assertEquals($this->subjectMock, $this->model->afterSave($this->subjectMock));
    }

    public function testAfterDeleteNonScheduled()
    {
        $this->indexerMock->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('isScheduled')
            ->will($this->returnValue(false));
        $this->indexerMock->expects($this->once())
            ->method('reindexList')
            ->with([1]);

        $this->subjectMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->assertEquals($this->subjectMock, $this->model->afterDelete($this->subjectMock));
    }

    public function testAfterDeleteScheduled()
    {
        $this->indexerMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->indexerMock->expects($this->once())
            ->method('isScheduled')
            ->will($this->returnValue(true));
        $this->indexerMock->expects($this->never())
            ->method('reindexList');

        $this->subjectMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->assertEquals($this->subjectMock, $this->model->afterDelete($this->subjectMock));
    }
}
