<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\ProductLink;

use Magento\Catalog\Api\Data\ProductLinkInterface;

class ManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\ProductLink\Management
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */

    protected $productRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkInitializerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productLinkBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productResourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

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
            '\Magento\Catalog\Api\Data\ProductLinkInterfaceDataBuilder',
            [],
            [],
            '',
            false
        );
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject(
            '\Magento\Catalog\Model\ProductLink\Management',
            [
                'productRepository' => $this->productRepositoryMock,
                'collectionProvider' => $this->collectionProviderMock,
                'productLinkBuilder' => $this->productLinkBuilderMock,
                'linkInitializer' => $this->linkInitializerMock,
                'productResource' => $this->productResourceMock
            ]
        );
    }
    
    public function testGetLinkedItemsByType()
    {
        $productSku = 'product';
        $linkType = 'link';
        $this->productRepositoryMock->expects($this->once())->method('get')->with($productSku)
            ->willReturn($this->productMock);
        $item = [
            'sku' => 'product1',
            'type' => 'type1',
            'position' => 'pos1',
        ];
        $itemCollection = [$item];
        $expectedItem = [
            ProductLinkInterface::LINKED_PRODUCT_SKU => $item['sku'],
            ProductLinkInterface::LINKED_PRODUCT_TYPE => $item['type'],
            ProductLinkInterface::POSITION => $item['position'],
            ProductLinkInterface::PRODUCT_SKU => $productSku,
            ProductLinkInterface::LINK_TYPE => $linkType
        ];
        $this->collectionProviderMock->expects($this->once())
            ->method('getCollection')
            ->with($this->productMock, $linkType)
            ->willReturn($itemCollection);
        $this->productMock->expects($this->once())->method('getSku')->willReturn($productSku);
        $this->productLinkBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->with($expectedItem)
            ->willReturnSelf();
        $this->productLinkBuilderMock->expects($this->once())->method('create')->willReturn('test');
        $this->assertEquals(['test'], $this->model->getLinkedItemsByType($productSku, $linkType));
    }

    public function testSetProductLinks()
    {
        $type = 'type';
        $linkedProductsMock = [];
        $linksData = [];
        for ($i = 0; $i < 2; $i++) {
            $linkMock = $this->getMockForAbstractClass(
                '\Magento\Catalog\Api\Data\ProductLinkInterface',
                [],
                '',
                false,
                false,
                true,
                ['getLinkedProductSku', '__toArray']
            );
            $linkMock->expects($this->exactly(2))
                ->method('getLinkedProductSku')
                ->willReturn('linkedProduct' . $i .'Sku');
            $linkMock->expects($this->once())->method('__toArray');
            $linkedProductsMock[$i] = $linkMock;
            $linksData['productSku']['link'][] = $linkMock;
        }
        $linkedSkuList = ['linkedProduct0Sku', 'linkedProduct1Sku'];
        $linkedProductIds = ['linkedProduct0Sku' => 1, 'linkedProduct1Sku' => 2];

        $this->productResourceMock->expects($this->once())->method('getProductsIdsBySkus')->with($linkedSkuList)
            ->willReturn($linkedProductIds);
        $this->productRepositoryMock->expects($this->once())->method('get')->willReturn($this->productMock);
        $this->linkInitializerMock->expects($this->once())->method('initializeLinks')
            ->with($this->productMock, [$type => [
                1 => ['product_id' => 1],
                2 => ['product_id' => 2]
            ]]);
        $this->productMock->expects($this->once())->method('save');
        $this->assertTrue($this->model->setProductLinks('', $type, $linkedProductsMock));
    }

    /**
     * @dataProvider setProductLinksNoProductExceptionDataProvider
     */
    public function testSetProductLinksNoProductException($exceptionName, $exceptionMessage, $linkedProductIds)
    {
        $this->setExpectedException($exceptionName, $exceptionMessage);
        $linkedProductsMock = [];
        for ($i = 0; $i < 2; $i++) {
            $productLinkMock = $this->getMock('\Magento\Catalog\Api\Data\ProductLinkInterface');
            $productLinkMock->expects($this->any())
                ->method('getLinkedProductSku')
                ->willReturn('linkedProduct' . $i .'Sku');
            $productLinkMock->expects($this->any())->method('getProductSku')->willReturn('productSku');
            $productLinkMock->expects($this->any())->method('getLinkType')->willReturn('link');
            $linkedProductsMock[$i] = $productLinkMock;
        }
        $linkedSkuList = ['linkedProduct0Sku', 'linkedProduct1Sku'];
        $this->productResourceMock->expects($this->any())->method('getProductsIdsBySkus')->with($linkedSkuList)
            ->willReturn($linkedProductIds);
        $this->model->setProductLinks('', '', $linkedProductsMock);
    }

    public function setProductLinksNoProductExceptionDataProvider()
    {
        return [
            [
                '\Magento\Framework\Exception\NoSuchEntityException',
                'Product with SKU "linkedProduct0Sku" does not exist',
                ['linkedProduct1Sku' => 2]
            ], [
                '\Magento\Framework\Exception\NoSuchEntityException',
                'Product with SKU "linkedProduct1Sku" does not exist',
                ['linkedProduct0Sku' => 1]
            ]
        ];
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Invalid data provided for linked products
     */
    public function testSetProductLinksInvalidDataException()
    {
        $type = 'type';
        $linkedProductsMock = [];
        $linksData = [];
        for ($i = 0; $i < 2; $i++) {
            $linkMock = $this->getMockForAbstractClass(
                '\Magento\Catalog\Api\Data\ProductLinkInterface',
                [],
                '',
                false,
                false,
                true,
                ['getLinkedProductSku', '__toArray']
            );
            $linkMock->expects($this->exactly(2))
                ->method('getLinkedProductSku')
                ->willReturn('linkedProduct' . $i .'Sku');
            $linkMock->expects($this->once())->method('__toArray');
            $linkedProductsMock[$i] = $linkMock;
            $linksData['productSku']['link'][] = $linkMock;
        }
        $linkedSkuList = ['linkedProduct0Sku', 'linkedProduct1Sku'];
        $linkedProductIds = ['linkedProduct0Sku' => 1, 'linkedProduct1Sku' => 2];

        $this->productResourceMock->expects($this->once())->method('getProductsIdsBySkus')->with($linkedSkuList)
            ->willReturn($linkedProductIds);
        $this->productRepositoryMock->expects($this->once())->method('get')->willReturn($this->productMock);
        $this->linkInitializerMock->expects($this->once())->method('initializeLinks')
            ->with($this->productMock, [$type => [
                1 => ['product_id' => 1],
                2 => ['product_id' => 2]
            ]]);
        $this->productMock->expects($this->once())->method('save')->willThrowException(new \Exception());
        $this->model->setProductLinks('', $type, $linkedProductsMock);
    }
}
