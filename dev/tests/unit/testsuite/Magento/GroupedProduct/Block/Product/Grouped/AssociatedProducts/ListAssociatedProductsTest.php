<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Block\Product\Grouped\AssociatedProducts;

class ListAssociatedProductsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeInstanceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Magento\GroupedProduct\Block\Product\Grouped\AssociatedProducts\ListAssociatedProducts
     */
    protected $block;

    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Backend\Block\Template\Context', array(), array(), '', false);
        $this->registryMock = $this->getMock('Magento\Registry', array(), array(), '', false);
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->storeMock = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', array(), array(), '', false);
        $this->typeInstanceMock = $this->getMock(
            'Magento\GroupedProduct\Model\Product\Type\Grouped', array(), array(), '', false
        );

        $this->contextMock->expects($this->any())
            ->method('getStoreManager')
            ->will($this->returnValue($this->storeManagerMock));

        $this->block = new ListAssociatedProducts($this->contextMock, $this->registryMock);
    }

    /**
     * @covers Magento\GroupedProduct\Block\Product\Grouped\AssociatedProducts\ListAssociatedProducts
     *     ::getAssociatedProducts
     */
    public function testGetAssociatedProducts()
    {
        $this->storeMock->expects($this->any())
            ->method('formatPrice')
            ->with('1.00', false)
            ->will($this->returnValue('1'));

        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->storeMock));

        $this->productMock->expects($this->once())
            ->method('getTypeInstance')
            ->will($this->returnValue($this->typeInstanceMock));

        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('current_product')
            ->will($this->returnValue($this->productMock));

        $this->typeInstanceMock->expects($this->once())
            ->method('getAssociatedProducts')
            ->with($this->productMock)
            ->will($this->returnValue(array($this->generateAssociatedProduct(1), $this->generateAssociatedProduct(2))));

        $expectedResult = array(
            '0' => array(
                'id' => 'id1',
                'sku' => 'sku1',
                'name' => 'name1',
                'qty' => 1,
                'position' => 1,
                'price' => '1'
            ),
            '1' => array(
                'id' => 'id2',
                'sku' => 'sku2',
                'name' => 'name2',
                'qty' => 2,
                'position' => 2,
                'price' => '1'
            )
        );

        $this->assertEquals($expectedResult, $this->block->getAssociatedProducts());
    }

    /**
     * Generate associated product mock
     *
     * @param int $productKey
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function generateAssociatedProduct($productKey = 0)
    {
        $associatedProduct = $this->getMock('Magento\Object',
            array('getQty', 'getPosition', 'getId', 'getSku', 'getName', 'getPrice'), array(), '', false
        );

        $associatedProduct->expects($this->once())->method('getId')->will($this->returnValue('id' . $productKey));
        $associatedProduct->expects($this->once())->method('getSku')->will($this->returnValue('sku' . $productKey));
        $associatedProduct->expects($this->once())->method('getName')->will($this->returnValue('name' . $productKey));
        $associatedProduct->expects($this->once())->method('getQty')->will($this->returnValue($productKey));
        $associatedProduct->expects($this->once())->method('getPosition')->will($this->returnValue($productKey));
        $associatedProduct->expects($this->once())->method('getPrice')->will($this->returnValue('1.00'));

        return $associatedProduct;
    }
}
