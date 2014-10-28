<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

use Magento\TestFramework\Helper\ObjectManager;

class ProductRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $initializationHelperMock;

    /**
     * @var \Magento\Catalog\Model\Resource\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceModelMock;

    /**
     * @var \Magento\Catalog\Model\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    protected function setUp()
    {
        $this->productFactoryMock = $this->getMock(
            'Magento\Catalog\Model\ProductFactory',
            array('create'),
            [],
            '',
            false
        );
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->initializationHelperMock = $this->getMock(
            '\Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper',
            [],
            [],
            '',
            false
        );
        $this->resourceModelMock = $this->getMock('\Magento\Catalog\Model\Resource\Product', [], [], '', false);
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            'Magento\Catalog\Model\ProductRepository',
            [
                'productFactory' => $this->productFactoryMock,
                'initializationHelper' => $this->initializationHelperMock,
                'resourceModel' => $this->resourceModelMock
            ]
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Requested product doesn't exist
     */
    public function testCreateThrowsExceptionIfNoSuchProduct()
    {
        $this->productFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->once())->method('getIdBySku')->with('test_sku')
            ->will($this->returnValue(null));
        $this->model->get('test_sku');
    }

    public function testCreateCreatesProduct()
    {
        $this->productFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->once())->method('getIdBySku')->with('test_sku')
            ->will($this->returnValue('test_id'));
        $this->productMock->expects($this->once())->method('load')->with('test_id');
        $this->assertSame($this->productMock, $this->model->get('test_sku'));
        $this->assertSame($this->productMock, $this->model->get('test_sku'));
    }

    public function testCreateCreatesProductInEditMode()
    {
        $this->productFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->once())->method('getIdBySku')->with('test_sku')
            ->will($this->returnValue('test_id'));
        $this->productMock->expects($this->once())->method('setData')->with('_edit_mode', true);
        $this->productMock->expects($this->once())->method('load')->with('test_id');
        $this->assertSame($this->productMock, $this->model->get('test_sku', ['edit_mode' => true]));
    }

    public function testSave()
    {
        $this->initializationHelperMock->expects($this->once())->method('initialize')->with($this->productMock);
        $this->resourceModelMock->expects($this->once())->method('validate')->with($this->productMock)
            ->willReturn(true);
        $this->resourceModelMock->expects($this->once())->method('save')->with($this->productMock)->willReturn(true);
        $this->productMock->expects($this->once())->method('getId')->willReturn(42);
        $this->assertEquals($this->productMock, $this->model->save($this->productMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Unable to save product
     */
    public function testSaveUnableToSaveException()
    {
        $this->initializationHelperMock->expects($this->once())->method('initialize')->with($this->productMock);
        $this->resourceModelMock->expects($this->once())->method('validate')->with($this->productMock)
            ->willReturn(true);
        $this->resourceModelMock->expects($this->once())->method('save')->with($this->productMock)->willReturn(true);
        $this->productMock->expects($this->once())->method('getId')->willReturn(false);
        $this->model->save($this->productMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Invalid value of "" provided for the  field.
     */
    public function testSaveException()
    {
        $this->initializationHelperMock->expects($this->once())->method('initialize')->with($this->productMock);
        $this->resourceModelMock->expects($this->once())->method('validate')->with($this->productMock)
            ->willReturn(true);
        $this->resourceModelMock->expects($this->once())->method('save')->with($this->productMock)
            ->willThrowException(new \Magento\Eav\Model\Entity\Attribute\Exception('123'));
        $this->productMock->expects($this->never())->method('getId');
        $this->model->save($this->productMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Invalid product data
     */
    public function testSaveInvalidProductException()
    {
        $this->initializationHelperMock->expects($this->once())->method('initialize')->with($this->productMock);
        $this->resourceModelMock->expects($this->once())->method('validate')->with($this->productMock)
            ->willReturn(false);
        $this->productMock->expects($this->never())->method('getId');
        $this->model->save($this->productMock);
    }

    public function testDelete()
    {
        $this->productMock->expects($this->once())->method('getSku')->willReturn('product-42');
        $this->resourceModelMock->expects($this->once())->method('delete')->with($this->productMock)
            ->willReturn(true);
        $this->assertTrue($this->model->delete($this->productMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Unable to remove product product-42
     */
    public function testDeleteException()
    {
        $this->productMock->expects($this->once())->method('getSku')->willReturn('product-42');
        $this->resourceModelMock->expects($this->once())->method('delete')->with($this->productMock)
            ->willThrowException(new \Exception);
        $this->model->delete($this->productMock);
    }

    public function testDeleteBySku()
    {
        $sku = 'product-42';
        $this->productFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->once())->method('getIdBySku')->with($sku)
            ->will($this->returnValue('42'));
        $this->productMock->expects($this->once())->method('load')->with('42');
        $this->assertTrue($this->model->deleteBySku($sku));
    }
}
