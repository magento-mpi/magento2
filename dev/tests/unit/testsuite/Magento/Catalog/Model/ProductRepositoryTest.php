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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    protected function setUp()
    {
        $this->productFactoryMock = $this->getMock('Magento\Catalog\Model\ProductFactory', ['create'], [], '', false);
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
    public function testGetAbsentProduct()
    {
        $this->productFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->once())->method('getIdBySku')->with('test_sku')
            ->will($this->returnValue(null));
        $this->productFactoryMock->expects($this->never())->method('setData');
        $this->model->get('test_sku');
    }

    public function testCreateCreatesProduct()
    {
        $this->productFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->once())->method('getIdBySku')->with('test_sku')
            ->will($this->returnValue('test_id'));
        $this->resourceModelMock->expects($this->once())->method('load')->with($this->productMock, 'test_id');
        $this->assertEquals($this->productMock, $this->model->get('test_sku'));
    }

    public function testGetProductInEditMode()
    {
        $this->productFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->once())->method('getIdBySku')->with('test_sku')
            ->will($this->returnValue('test_id'));
        $this->productMock->expects($this->once())->method('setData')->with('_edit_mode', true);
        $this->resourceModelMock->expects($this->once())->method('load')->with($this->productMock, 'test_id');
        $this->assertEquals($this->productMock, $this->model->get('test_sku', true));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Requested product doesn't exist
     */
    public function testGetByIdAbsentProduct()
    {
        $this->productFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->productMock));
        $this->resourceModelMock->expects($this->once())->method('load')->with($this->productMock, 'product_id');
        $this->productMock->expects($this->once())->method('getId')->willReturn(null);
        $this->model->getById('product_id');
    }

    public function testGetByIdProductInEditMode()
    {
        $productId = 123;
        $this->productFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->once())->method('setData')->with('_edit_mode', true);
        $this->resourceModelMock->expects($this->once())->method('load')->with($this->productMock, $productId);
        $this->productMock->expects($this->once())->method('getId')->willReturn($productId);
        $this->assertEquals($this->productMock, $this->model->getById($productId, true));
    }

    public function testGetByIdWithSetStoreId()
    {
        $productId = 123;
        $storeId = 1;
        $this->productFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->once())->method('setData')->with('store_id', $storeId);
        $this->resourceModelMock->expects($this->once())->method('load')->with($this->productMock, $productId);
        $this->productMock->expects($this->once())->method('getId')->willReturn($productId);
        $this->assertEquals($this->productMock, $this->model->getById($productId, false, $storeId));
    }

    public function testGetBySkuFromCacheInitializedInGetById()
    {
        $productId = 123;
        $productSku = 'product_123';
        $this->productFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->productMock));
        $this->resourceModelMock->expects($this->once())->method('load')->with($this->productMock, $productId);
        $this->productMock->expects($this->once())->method('getId')->willReturn($productId);
        $this->productMock->expects($this->once())->method('getSku')->willReturn($productSku);
        $this->assertEquals($this->productMock, $this->model->getById($productId));
        $this->assertEquals($this->productMock, $this->model->get($productSku));
    }

    public function testSave()
    {
        $this->initializationHelperMock->expects($this->once())->method('initialize')->with($this->productMock);
        $this->resourceModelMock->expects($this->once())->method('validate')->with($this->productMock)
            ->willReturn(true);
        $this->resourceModelMock->expects($this->once())->method('save')->with($this->productMock)->willReturn(true);
        $this->assertEquals($this->productMock, $this->model->save($this->productMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Unable to save product
     */
    public function testSaveUnableToSaveException()
    {
        $this->initializationHelperMock->expects($this->once())->method('initialize')->with($this->productMock);
        $this->resourceModelMock->expects($this->once())->method('validate')->with($this->productMock)
            ->willReturn(true);
        $this->resourceModelMock->expects($this->once())->method('save')->with($this->productMock)
            ->willThrowException(new \Exception);
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
     * @expectedExceptionMessage Invalid product data: error1,error2
     */
    public function testSaveInvalidProductException()
    {
        $this->initializationHelperMock->expects($this->once())->method('initialize')->with($this->productMock);
        $this->resourceModelMock->expects($this->once())->method('validate')->with($this->productMock)
            ->willReturn(['error1', 'error2']);
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

    public function testDeleteById()
    {
        $sku = 'product-42';
        $this->productFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->once())->method('getIdBySku')->with($sku)
            ->will($this->returnValue('42'));
        $this->resourceModelMock->expects($this->once())->method('load')->with($this->productMock, '42');
        $this->assertTrue($this->model->deleteById($sku));
    }
}
