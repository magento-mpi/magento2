<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model;

class CategoryLinkRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\CategoryLinkRepository
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepositoryMock;

    protected function setUp()
    {
        $this->categoryRepositoryMock = $this->getMock('\Magento\Catalog\Model\CategoryRepository', [], [], '', false);
        $this->productRepositoryMock = $this->getMock('\Magento\Catalog\Model\ProductRepository', [], [], '', false);
        $this->model = new \Magento\Catalog\Model\CategoryLinkRepository(
            $this->categoryRepositoryMock,
            $this->productRepositoryMock
        );
    }

    public function testSave()
    {
        $categoryId = 42;
        $productId = 55;
        $productPosition = 1;
        $sku = 'testSku';
        $productPositions = [$productId => $productPosition];
        $categoryMock = $this->getMockForAbstractClass(
            '\Magento\Catalog\Api\Data\CategoryInterface',
            [],
            '',
            false,
            false,
            true,
            ['getProductsPosition', 'setPostedProducts', 'save']
        );
        $productMock = $this->getMockForAbstractClass(
            '\Magento\Catalog\Api\Data\ProductInterface',
            [],
            '',
            false,
            false,
            true,
            ['getId']
        );
        $productLinkMock = $this->getMock('\Magento\Catalog\Api\Data\CategoryProductLinkInterface');
        $productLinkMock->expects($this->once())->method('getCategoryId')->willReturn($categoryId);
        $productLinkMock->expects($this->once())->method('getSku')->willReturn($sku);
        $this->categoryRepositoryMock->expects($this->once())->method('get')->with($categoryId)
            ->willReturn($categoryMock);
        $this->productRepositoryMock->expects($this->once())->method('get')->with($sku)->willReturn($productMock);
        $categoryMock->expects($this->once())->method('getProductsPosition')->willReturn([]);
        $productMock->expects($this->once())->method('getId')->willReturn($productId);
        $productLinkMock->expects($this->once())->method('getPosition')->willReturn($productPosition);
        $categoryMock->expects($this->once())->method('setPostedProducts')->with($productPositions);
        $categoryMock->expects($this->once())->method('save');
        $this->assertTrue($this->model->save($productLinkMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Could not save product "55" with position 1 to category 42
     */
    public function testSaveWithCouldNotSaveException()
    {
        $categoryId = 42;
        $productId = 55;
        $productPosition = 1;
        $sku = 'testSku';
        $productPositions = [$productId => $productPosition];
        $categoryMock = $this->getMockForAbstractClass(
            '\Magento\Catalog\Api\Data\CategoryInterface',
            [],
            '',
            false,
            false,
            true,
            ['getProductsPosition', 'setPostedProducts', 'save', 'getId']
        );
        $productMock = $this->getMockForAbstractClass(
            '\Magento\Catalog\Api\Data\ProductInterface',
            [],
            '',
            false,
            false,
            true,
            ['getId']
        );
        $productLinkMock = $this->getMock('\Magento\Catalog\Api\Data\CategoryProductLinkInterface');
        $productLinkMock->expects($this->once())->method('getCategoryId')->willReturn($categoryId);
        $productLinkMock->expects($this->once())->method('getSku')->willReturn($sku);
        $this->categoryRepositoryMock->expects($this->once())->method('get')->with($categoryId)
            ->willReturn($categoryMock);
        $this->productRepositoryMock->expects($this->once())->method('get')->with($sku)->willReturn($productMock);
        $categoryMock->expects($this->once())->method('getProductsPosition')->willReturn([]);
        $productMock->expects($this->exactly(2))->method('getId')->willReturn($productId);
        $productLinkMock->expects($this->exactly(2))->method('getPosition')->willReturn($productPosition);
        $categoryMock->expects($this->once())->method('setPostedProducts')->with($productPositions);
        $categoryMock->expects($this->once())->method('getId')->willReturn($categoryId);
        $categoryMock->expects($this->once())->method('save')->willThrowException(new \Exception());
        $this->model->save($productLinkMock);
    }

    public function testDeleteByIds()
    {
        $categoryId = "42";
        $productSku = "testSku";
        $productId = 55;
        $productPositions = [55 => 1];
        $categoryMock = $this->getMockForAbstractClass(
            '\Magento\Catalog\Api\Data\CategoryInterface',
            [],
            '',
            false,
            false,
            true,
            ['getProductsPosition', 'setPostedProducts', 'save', 'getId']
        );
        $productMock = $this->getMockForAbstractClass(
            '\Magento\Catalog\Api\Data\ProductInterface',
            [],
            '',
            false,
            false,
            true,
            ['getId']
        );
        $this->categoryRepositoryMock->expects($this->once())->method('get')->with($categoryId)
            ->willReturn($categoryMock);
        $this->productRepositoryMock->expects($this->once())->method('get')->with($productSku)
            ->willReturn($productMock);
        $categoryMock->expects($this->once())->method('getProductsPosition')->willReturn($productPositions);
        $productMock->expects($this->once())->method('getId')->willReturn($productId);
        $categoryMock->expects($this->once())->method('setPostedProducts')->with([]);
        $categoryMock->expects($this->once())->method('save');
        $this->assertTrue($this->model->deleteByIds($categoryId, $productSku));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Could not save product "55" with position %position to category 42
     */
    public function testDeleteByIdsWithCouldNotSaveException()
    {
        $categoryId = "42";
        $productSku = "testSku";
        $productId = 55;
        $productPositions = [55 => 1];
        $categoryMock = $this->getMockForAbstractClass(
            '\Magento\Catalog\Api\Data\CategoryInterface',
            [],
            '',
            false,
            false,
            true,
            ['getProductsPosition', 'setPostedProducts', 'save', 'getId']
        );
        $productMock = $this->getMockForAbstractClass(
            '\Magento\Catalog\Api\Data\ProductInterface',
            [],
            '',
            false,
            false,
            true,
            ['getId']
        );
        $this->categoryRepositoryMock->expects($this->once())->method('get')->with($categoryId)
            ->willReturn($categoryMock);
        $this->productRepositoryMock->expects($this->once())->method('get')->with($productSku)
            ->willReturn($productMock);
        $categoryMock->expects($this->once())->method('getProductsPosition')->willReturn($productPositions);
        $productMock->expects($this->exactly(2))->method('getId')->willReturn($productId);
        $categoryMock->expects($this->once())->method('setPostedProducts')->with([]);
        $categoryMock->expects($this->once())->method('getId')->willReturn($categoryId);
        $categoryMock->expects($this->once())->method('save')->willThrowException(new \Exception());
        $this->model->deleteByIds($categoryId, $productSku);
    }

    public function testDelete()
    {
        $categoryId = "42";
        $productSku = "testSku";
        $productId = 55;
        $productPositions = [55 => 1];
        $productLinkMock = $this->getMock('\Magento\Catalog\Api\Data\CategoryProductLinkInterface');
        $productLinkMock->expects($this->once())->method('getCategoryId')->willReturn($categoryId);
        $productLinkMock->expects($this->once())->method('getSku')->willReturn($productSku);
        $categoryMock = $this->getMockForAbstractClass(
            '\Magento\Catalog\Api\Data\CategoryInterface',
            [],
            '',
            false,
            false,
            true,
            ['getProductsPosition', 'setPostedProducts', 'save', 'getId']
        );
        $productMock = $this->getMockForAbstractClass(
            '\Magento\Catalog\Api\Data\ProductInterface',
            [],
            '',
            false,
            false,
            true,
            ['getId']
        );
        $this->categoryRepositoryMock->expects($this->once())->method('get')->with($categoryId)
            ->willReturn($categoryMock);
        $this->productRepositoryMock->expects($this->once())->method('get')->with($productSku)
            ->willReturn($productMock);
        $categoryMock->expects($this->once())->method('getProductsPosition')->willReturn($productPositions);
        $productMock->expects($this->once())->method('getId')->willReturn($productId);
        $categoryMock->expects($this->once())->method('setPostedProducts')->with([]);
        $categoryMock->expects($this->once())->method('save');
        $this->assertTrue($this->model->delete($productLinkMock));
    }
}
