<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product;

class ProductLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProductLoader
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $factoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var string
     */
    protected $productSku = 'simple-sku';

    protected function setUp()
    {
        $this->factoryMock = $this->getMock('\Magento\Catalog\Model\ProductFactory', ['create'], [], '', false);
        $this->productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $this->model = new ProductLoader($this->factoryMock);
    }

    public function testLoad()
    {
        $this->factoryMock->expects($this->once())->method('create')->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->once())
            ->method('getIdBySku')
            ->with($this->productSku)
            ->will($this->returnValue(1));

        $this->productMock->expects($this->once())->method('load')->with(1);
        $this->assertEquals($this->productMock, $this->model->load($this->productSku));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage There is no product with provided SKU
     */
    public function testLoadWithNonExistedProduct()
    {
        $this->factoryMock->expects($this->once())->method('create')->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->once())
            ->method('getIdBySku')
            ->with($this->productSku)
            ->will($this->returnValue(null));

        $this->productMock->expects($this->never())->method('load');

        $this->assertEquals($this->productMock, $this->model->load($this->productSku));
    }
}
