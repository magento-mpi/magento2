<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\ProductLink;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\ProductLink\Repository
     */
    protected $model;

    /**
     * @var \Magento\Catalog\Model\ProductRepository|\PHPUnit_Framework_MockObject_MockObject
     */

    protected $productRepositoryMock;

    /**
     * @var \Magento\Catalog\Model\ProductLink\CollectionProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityCollectionProviderMock;

    /**
     * @var \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkInitializerMock;


    protected function setUp()
    {
        $this->productRepositoryMock = $this->getMock('\Magento\Catalog\Model\ProductRepository', [], [], '', false);
        $this->entityCollectionProviderMock = $this->getMock(
            '\Magento\Catalog\Model\ProductLink\CollectionProvider',
            [],
            [],
            '',
            false
        );
        $this->linkInitializerMock = $this->getMock(
            '\Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks',
            [],
            [],
            '',
            false
        );
        $this->model = new \Magento\Catalog\Model\ProductLink\Repository(
            $this->productRepositoryMock,
            $this->entityCollectionProviderMock,
            $this->linkInitializerMock
        );
    }

    public function testSave()
    {
        $entityMock = $this->getMock('\Magento\Catalog\Api\Data\ProductLinkInterface');
        $productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $linkedProductMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $this->productRepositoryMock->expects($this->exactly(2))->method('get')->will($this->returnValueMap(
            [
                ['product', false, $productMock],
                ['linkedProduct', false, $linkedProductMock]
            ]
        ));
        $entityMock->expects($this->once())->method('getLinkedProductSku')->willReturn('linkedProduct');
        $entityMock->expects($this->once())->method('getProductSku')->willReturn('product');
        $entityMock->expects($this->any())->method('getLinkType')->willReturn('linkType');
        $entityMock->expects($this->any())->method('__toArray')->willReturn([]);
        $linkedProductMock->expects($this->any())->method('getId')->willReturn(42);
        $this->entityCollectionProviderMock->expects($this->once())->method('getCollection')->willReturn([]);
        $this->linkInitializerMock->expects($this->once())->method('initializeLinks')->with($productMock, [
            'linkType' => [42 => ['product_id' => 42]]
        ]);
        $this->assertTrue($this->model->save($entityMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Invalid data provided for linked products
     */
    public function testSaveWithException()
    {
        $entityMock = $this->getMock('\Magento\Catalog\Api\Data\ProductLinkInterface');
        $productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $linkedProductMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $this->productRepositoryMock->expects($this->exactly(2))->method('get')->will($this->returnValueMap(
            [
                ['product', false, $productMock],
                ['linkedProduct', false, $linkedProductMock]
            ]
        ));
        $entityMock->expects($this->once())->method('getLinkedProductSku')->willReturn('linkedProduct');
        $entityMock->expects($this->once())->method('getProductSku')->willReturn('product');
        $entityMock->expects($this->any())->method('getLinkType')->willReturn('linkType');
        $entityMock->expects($this->any())->method('__toArray')->willReturn([]);
        $linkedProductMock->expects($this->any())->method('getId')->willReturn(42);
        $this->entityCollectionProviderMock->expects($this->once())->method('getCollection')->willReturn([]);
        $this->linkInitializerMock->expects($this->once())->method('initializeLinks')->with($productMock, [
            'linkType' => [42 => ['product_id' => 42]]
        ]);
        $productMock->expects($this->once())->method('save')->willThrowException(new \Exception);
        $this->model->save($entityMock);
    }

    public function testDelete()
    {
        $entityMock = $this->getMock('\Magento\Catalog\Api\Data\ProductLinkInterface');
        $productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $linkedProductMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $this->productRepositoryMock->expects($this->exactly(2))->method('get')->will($this->returnValueMap(
            [
                ['product', false, $productMock],
                ['linkedProduct', false, $linkedProductMock]
            ]
        ));
        $entityMock->expects($this->once())->method('getLinkedProductSku')->willReturn('linkedProduct');
        $entityMock->expects($this->once())->method('getProductSku')->willReturn('product');
        $entityMock->expects($this->any())->method('getLinkType')->willReturn('linkType');
        $entityMock->expects($this->any())->method('__toArray')->willReturn([]);
        $linkedProductMock->expects($this->any())->method('getId')->willReturn(42);
        $this->entityCollectionProviderMock->expects($this->once())->method('getCollection')->willReturn([
            42 => ''
        ]);
        $this->linkInitializerMock->expects($this->once())->method('initializeLinks')->with($productMock, [
            'linkType' => [42 => ['product_id' => 42]]
        ]);
        $this->assertTrue($this->model->delete($entityMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Invalid data provided for linked products
     */
    public function testDeleteWithInvalidDataException()
    {
        $entityMock = $this->getMock('\Magento\Catalog\Api\Data\ProductLinkInterface');
        $productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $linkedProductMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $this->productRepositoryMock->expects($this->exactly(2))->method('get')->will($this->returnValueMap(
            [
                ['product', false, $productMock],
                ['linkedProduct', false, $linkedProductMock]
            ]
        ));
        $entityMock->expects($this->once())->method('getLinkedProductSku')->willReturn('linkedProduct');
        $entityMock->expects($this->once())->method('getProductSku')->willReturn('product');
        $entityMock->expects($this->any())->method('getLinkType')->willReturn('linkType');
        $entityMock->expects($this->any())->method('__toArray')->willReturn([]);
        $linkedProductMock->expects($this->any())->method('getId')->willReturn(42);
        $this->entityCollectionProviderMock->expects($this->once())->method('getCollection')->willReturn([
            42 => ''
        ]);
        $this->linkInitializerMock->expects($this->once())->method('initializeLinks')->with($productMock, [
            'linkType' => [42 => ['product_id' => 42]]
        ]);
        $productMock->expects($this->once())->method('save')->willThrowException(new \Exception);
        $this->model->delete($entityMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Product with SKU linkedProduct is not linked to product with SKU product
     */
    public function testDeleteWithNoSuchEntityException()
    {
        $entityMock = $this->getMock('\Magento\Catalog\Api\Data\ProductLinkInterface');
        $productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $linkedProductMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $this->productRepositoryMock->expects($this->exactly(2))->method('get')->will($this->returnValueMap(
            [
                ['product', false, $productMock],
                ['linkedProduct', false, $linkedProductMock]
            ]
        ));
        $entityMock->expects($this->exactly(2))->method('getLinkedProductSku')->willReturn('linkedProduct');
        $entityMock->expects($this->exactly(2))->method('getProductSku')->willReturn('product');
        $entityMock->expects($this->any())->method('getLinkType')->willReturn('linkType');
        $this->model->delete($entityMock);
    }
}
