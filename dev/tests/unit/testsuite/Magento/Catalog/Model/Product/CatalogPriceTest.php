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
    protected $priceFactoryMock;

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
        $this->priceFactoryMock = $this->getMock(
            'Magento\Catalog\Model\Product\CatalogPriceFactory',
            array(),
            array(),
            '',
            false
        );
        $this->productMock = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->catalogPriceInterfaceMock = $this->getMock('Magento\Catalog\Model\Product\CatalogPriceInterface');
        $this->model = new \Magento\Catalog\Model\Product\CatalogPrice(
            $this->priceFactoryMock,
            array('custom_product_type' => 'CustomProduct/Model/CatalogPrice')
        );
    }

    public function testGetCatalogPriceWhenPoolContainsPriceModelForGivenProductType()
    {
        $this->productMock->expects(
            $this->any()
        )->method(
            'getTypeId'
        )->will(
            $this->returnValue('custom_product_type')
        );
        $this->priceFactoryMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'CustomProduct/Model/CatalogPrice'
        )->will(
            $this->returnValue($this->catalogPriceInterfaceMock)
        );
        $this->catalogPriceInterfaceMock->expects($this->once())->method('getCatalogPrice');
        $this->productMock->expects($this->never())->method('getFinalPrice');
        $this->model->getCatalogPrice($this->productMock);
    }

    public function testGetCatalogPriceWhenPoolDoesNotContainPriceModelForGivenProductType()
    {
        $this->productMock->expects($this->any())->method('getTypeId')->will($this->returnValue('test'));
        $this->priceFactoryMock->expects($this->never())->method('create');
        $this->productMock->expects($this->once())->method('getFinalPrice');
        $this->catalogPriceInterfaceMock->expects($this->never())->method('getCatalogPrice');
        $this->model->getCatalogPrice($this->productMock);
    }

    public function testGetCatalogRegularPriceWhenPoolDoesNotContainPriceModelForGivenProductType()
    {
        $this->productMock->expects($this->any())->method('getTypeId')->will($this->returnValue('test'));
        $this->priceFactoryMock->expects($this->never())->method('create');
        $this->catalogPriceInterfaceMock->expects($this->never())->method('getCatalogRegularPrice');
        $this->productMock->expects($this->once())->method('getPrice');
        $this->model->getCatalogRegularPrice($this->productMock);
    }

    public function testGetCatalogRegularPriceWhenPoolContainsPriceModelForGivenProductType()
    {
        $this->productMock->expects(
            $this->any()
        )->method(
            'getTypeId'
        )->will(
            $this->returnValue('custom_product_type')
        );
        $this->priceFactoryMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'CustomProduct/Model/CatalogPrice'
        )->will(
            $this->returnValue($this->catalogPriceInterfaceMock)
        );
        $this->catalogPriceInterfaceMock->expects($this->once())->method('getCatalogRegularPrice');
        $this->productMock->expects($this->never())->method('getPrice');
        $this->model->getCatalogRegularPrice($this->productMock);
    }
}
