<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product;

class CatalogPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\CatalogPrice
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogPriceInterfaceMock;

    public function setUp()
    {
        $this->objectManagerMock = $this->getMock('\Magento\App\ObjectManager', array(), array(), '', false);
        $this->productMock = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->catalogPriceInterfaceMock = $this->getMock('Magento\Catalog\Model\Product\CatalogPriceInterface');
        $this->model = new \Magento\Catalog\Model\Product\CatalogPrice(
            $this->objectManagerMock,
            array('product_type_id' => 'value')
        );
    }

    public function testGetCatalogPricePoolContainsProductId()
    {
        $this->productMock->expects($this->any())->method('getTypeId')->will($this->returnValue('product_type_id'));
        $this->objectManagerMock->expects($this->once())
            ->method('get')->with('value')->will($this->returnValue($this->catalogPriceInterfaceMock));
        $this->catalogPriceInterfaceMock->expects($this->once())->method('getCatalogPrice');
        $this->productMock->expects($this->never())->method('getFinalPrice');
        $this->model->getCatalogPrice($this->productMock);
    }

    public function testGetCatalogPricePoolDoesNotContainProductId()
    {
        $this->productMock->expects($this->any())->method('getTypeId')->will($this->returnValue('test'));
        $this->objectManagerMock->expects($this->never())->method('get');
        $this->productMock->expects($this->once())->method('getFinalPrice');
        $this->catalogPriceInterfaceMock->expects($this->never())->method('getCatalogPrice');
        $this->model->getCatalogPrice($this->productMock);
    }

    public function testGetCatalogRegularPriceDoesNotPoolContainProductId()
    {
        $this->productMock->expects($this->any())->method('getTypeId')->will($this->returnValue('test'));
        $this->objectManagerMock->expects($this->never())->method('get');
        $this->catalogPriceInterfaceMock->expects($this->never())->method('getCatalogRegularPrice');
        $this->productMock->expects($this->once())->method('getPrice');
        $this->model->getCatalogRegularPrice($this->productMock);
    }

    public function testGetCatalogRegularPricePoolContainsProductId()
    {
        $this->productMock->expects($this->any())->method('getTypeId')->will($this->returnValue('product_type_id'));
        $this->objectManagerMock->expects($this->once())
            ->method('get')->with('value')->will($this->returnValue($this->catalogPriceInterfaceMock));
        $this->catalogPriceInterfaceMock->expects($this->once())->method('getCatalogRegularPrice');
        $this->productMock->expects($this->never())->method('getPrice');
        $this->model->getCatalogRegularPrice($this->productMock);
    }
}
