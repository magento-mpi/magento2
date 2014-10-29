<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\ProductLink;

class ManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\ProductLink\Management
     */
    protected $model;

    /**
     * @var \Magento\Catalog\Model\ProductRepository|\PHPUnit_Framework_MockObject_MockObject
     */

    protected $productRepositoryMock;

    /**
     * @var \Magento\Catalog\Model\ProductLink\CollectionProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionProviderMock;

    /**
     * @var \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkInitializerMock;

    /**
     * @var \Magento\Catalog\Api\Data\ProductLinkInterfaceBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productLinkBuilderMock;

    /**
     * @var \Magento\Catalog\Model\Resource\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productResourceMock;

    protected function setUp()
    {
        $this->productRepositoryMock = $this->getMock('\Magento\Catalog\Model\ProductRepository', [], [], '', false);
        $this->productResourceMock = $this->getMock('\Magento\Catalog\Model\Resource\Product', [], [], '', false);
        $this->collectionProviderMock = $this->getMock(
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
        $this->productLinkBuilderMock = $this->getMock(
            '\Magento\Catalog\Api\Data\ProductLinkInterfaceBuilder',
            [],
            [],
            '',
            false
        );
        $this->model = new \Magento\Catalog\Model\ProductLink\Management(
            $this->productRepositoryMock,
            $this->collectionProviderMock,
            $this->productLinkBuilderMock,
            $this->linkInitializerMock,
            $this->productResourceMock
        );
    }
    
    public function testGetLinkedItemsByType()
    {
        $productSku = 'product';
        $linkType = 'link';
        $productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $abstractSimpleObjectMock = $this->getMock(
            '\Magento\Framework\Service\Data\AbstractSimpleObject',
            ['create'],
            [],
            '',
            false
        );

        $abstractSimpleObjectMock->expects($this->exactly(3))->method('create')->willReturn('test');
        $this->productRepositoryMock->expects($this->once())->method('get')->with($productSku)
            ->willReturn($productMock);
        $this->collectionProviderMock->expects($this->once())->method('getCollection')->with($productMock, $linkType)
            ->willReturn(
                [
                    ['sku' => 'product1', 'type' => 'type1', 'position' => 'pos1'],
                    ['sku' => 'product2', 'type' => 'type2', 'position' => 'pos2'],
                    ['sku' => 'product3', 'type' => 'type3', 'position' => 'pos3']
                ]
            );
        $productMock->expects($this->exactly(3))->method('getSku')->willReturn($productSku);
        $this->productLinkBuilderMock->expects($this->exactly(3))->method('populateWithArray')
            ->willReturn($abstractSimpleObjectMock);
        $this->assertEquals(['test', 'test', 'test'], $this->model->getLinkedItemsByType($productSku, $linkType));
    }

    public function testSetProductLinks()
    {
        $linkedProductsMock = [];
        $linkedSkuList = [];
        $linksData = [];
        $productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        for ($i = 0; $i < 2; $i++) {
            $productLinkMock = $this->getMock('\Magento\Catalog\Api\Data\ProductLinkInterface');
            $productLinkMock->expects($this->any())->method('getLinkedProductSku')->willReturn('linkedProductSku');
            $productLinkMock->expects($this->any())->method('getProductSku')->willReturn('productSku');
            $productLinkMock->expects($this->any())->method('getLinkType')->willReturn('link');
            $linkedProductsMock[$i] = $productLinkMock;
            $linksData['productSku']['link'][] = $productLinkMock;
            $linkedSkuList[] ='linkedProductSku';
            $linkedSkuList[] = 'productSku';
        }
        $linkedSkuList = array_unique($linkedSkuList);
        $linkedProductIds = ['linkedProductSku' => 1, 'productSku' => 2];

        $this->productResourceMock->expects($this->any())->method('getProductsIdsBySkus')->with($linkedSkuList)
            ->willReturn($linkedProductIds);
        $this->productRepositoryMock->expects($this->any())->method('get')->willReturn($productMock);
        $this->linkInitializerMock->expects($this->any())->method('initializeLinks');
        $productMock->expects($this->any())->method('save');
        $this->assertTrue($this->model->setProductLinks('', '', $linkedProductsMock));
    }

    /**
     * @dataProvider setProductLinksNoProductExceptionDataProvider
     */
    public function testSetProductLinksNoProductException($exceptionName, $exceptionMessage, $linkedProductIds)
    {
        $this->setExpectedException($exceptionName, $exceptionMessage);
        $linkedProductsMock = [];
        $linkedSkuList = [];
        for ($i = 0; $i < 2; $i++) {
            $productLinkMock = $this->getMock('\Magento\Catalog\Api\Data\ProductLinkInterface');
            $productLinkMock->expects($this->any())->method('getLinkedProductSku')->willReturn('linkedProductSku');
            $productLinkMock->expects($this->any())->method('getProductSku')->willReturn('productSku');
            $productLinkMock->expects($this->any())->method('getLinkType')->willReturn('link');
            $linkedProductsMock[$i] = $productLinkMock;
            $linkedSkuList[] ='linkedProductSku';
            $linkedSkuList[] = 'productSku';
        }
        $linkedSkuList = array_unique($linkedSkuList);
        $this->productResourceMock->expects($this->any())->method('getProductsIdsBySkus')->with($linkedSkuList)
            ->willReturn($linkedProductIds);
        $this->model->setProductLinks('', '', $linkedProductsMock);
    }

    public function setProductLinksNoProductExceptionDataProvider()
    {
        return [
            [
                '\Magento\Framework\Exception\NoSuchEntityException',
                'Product with SKU "linkedProductSku" does not exist',
                ['productSku' => 2]
            ], [
                '\Magento\Framework\Exception\NoSuchEntityException',
                'Product with SKU "productSku" does not exist',
                ['linkedProductSku' => 1]
            ]
        ];
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Invalid data provided for linked products
     */
    public function testSetProductLinksInvalidDataException()
    {
        $linkedProductsMock = [];
        $linkedSkuList = [];
        $linksData = [];
        for ($i = 0; $i < 2; $i++) {
            $productLinkMock = $this->getMock('\Magento\Catalog\Api\Data\ProductLinkInterface');
            $productLinkMock->expects($this->any())->method('getLinkedProductSku')->willReturn('linkedProductSku');
            $productLinkMock->expects($this->any())->method('getProductSku')->willReturn('productSku');
            $productLinkMock->expects($this->any())->method('getLinkType')->willReturn('link');
            $linkedProductsMock[$i] = $productLinkMock;
            $linksData['productSku']['link'][] = $productLinkMock;
            $linkedSkuList[] ='linkedProductSku';
            $linkedSkuList[] = 'productSku';
        }
        $linkedSkuList = array_unique($linkedSkuList);
        $linkedProductIds = ['linkedProductSku' => 1, 'productSku' => 2];

        $this->productResourceMock->expects($this->any())->method('getProductsIdsBySkus')->with($linkedSkuList)
            ->willReturn($linkedProductIds);
        $this->model->setProductLinks('', '', $linkedProductsMock);
    }
}
