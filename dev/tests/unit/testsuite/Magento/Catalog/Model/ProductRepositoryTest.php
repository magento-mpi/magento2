<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

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
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    protected function setUp()
    {
        $this->productFactoryMock = $this->getMock(
            'Magento\Catalog\Model\ProductFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->model = new ProductRepository($this->productFactoryMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testCreateThrowsExceptionIfNoSuchProduct()
    {
        $this->productFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->productMock));

        $this->productMock->expects($this->once())->method('getIdBySku')->with('test_sku')
            ->will($this->returnValue(null));
        $this->model->get('test_sku');
    }

    public function testCreateCreatesProduct()
    {
        $this->productFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValue($this->productMock));

        $this->productMock->expects($this->once())->method('getIdBySku')->with('test_sku')
            ->will($this->returnValue('test_id'));
        $this->productMock->expects($this->once())->method('load')->with('test_id');
        $this->productMock->expects($this->once())->method('getId')->will($this->returnValue('test_id'));
        $this->assertSame($this->productMock, $this->model->get('test_sku'));
        $this->assertSame($this->productMock, $this->model->get('test_sku'));
    }

    public function testCreateCreatesProductInEditMode()
    {
        $this->productFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValue($this->productMock));

        $this->productMock->expects($this->once())->method('getIdBySku')->with('test_sku')
            ->will($this->returnValue('test_id'));
        $this->productMock->expects($this->once())->method('setData')->with('_edit_mode', true);
        $this->productMock->expects($this->once())->method('getId')->will($this->returnValue('test_id'));
        $this->productMock->expects($this->once())->method('load')->with('test_id');
        $this->assertSame($this->productMock, $this->model->get('test_sku', true));
    }

    public function testGetByProductId()
    {
        $this->productFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->productMock));

        $this->productMock->expects($this->once())->method('load')->with('test_id');
        $this->productMock->expects($this->once())->method('getId')->will($this->returnValue('test_id'));
        $this->assertSame($this->productMock, $this->model->getByProductId('test_id'));
        $this->assertSame($this->productMock, $this->model->getByProductId('test_id'));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetByProductIdNotLoaded()
    {
        $this->productFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->productMock));

        $this->productMock->expects($this->once())->method('load')->with('test_id');
        $this->productMock->expects($this->once())->method('getId')->will($this->returnValue(null));
        $this->model->getByProductId('test_id');
    }
} 
