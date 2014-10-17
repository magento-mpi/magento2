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

    protected function setUp()
    {
        $productFactoryMock = $this->getMock(
            'Magento\Catalog\Model\ProductFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $productFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->productMock));
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            'Magento\Catalog\Model\ProductRepository',
            [
                'productFactory' => $productFactoryMock,
            ]
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testCreateThrowsExceptionIfNoSuchProduct()
    {
        $this->productMock->expects($this->once())->method('getIdBySku')->with('test_sku')
            ->will($this->returnValue(null));
        $this->model->get('test_sku');
    }

    public function testCreateCreatesProduct()
    {
        $this->productMock->expects($this->once())->method('getIdBySku')->with('test_sku')
            ->will($this->returnValue('test_id'));
        $this->productMock->expects($this->once())->method('load')->with('test_id');
        $this->assertSame($this->productMock, $this->model->get('test_sku'));
        $this->assertSame($this->productMock, $this->model->get('test_sku'));
    }

    public function testCreateCreatesProductInEditMode()
    {
        $this->productMock->expects($this->once())->method('getIdBySku')->with('test_sku')
            ->will($this->returnValue('test_id'));
        $this->productMock->expects($this->once())->method('setData')->with('_edit_mode', true);
        $this->productMock->expects($this->once())->method('load')->with('test_id');
        $this->assertSame($this->productMock, $this->model->get('test_sku', ['edit_mode' => true]));
    }
} 
