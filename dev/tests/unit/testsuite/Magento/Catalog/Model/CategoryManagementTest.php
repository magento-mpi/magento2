<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

class CategoryRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\CategoryManagement
     */
    protected $model;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryRepositoryMock;

    /**
     * @var \Magento\Catalog\Model\Category\Tree|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryTreeMock;

    protected function setUp()
    {
        $this->categoryRepositoryMock = $this->getMock('\Magento\Catalog\Model\CategoryRepository', [], [], '', false);
        $this->categoryTreeMock = $this->getMock('\Magento\Catalog\Model\Category\Tree', [], [], '', false);
        $this->model = new \Magento\Catalog\Model\CategoryManagement(
            $this->categoryRepositoryMock,
            $this->categoryTreeMock
        );
    }

    public function testGetTree()
    {
        $rootCategoryId = 1;
        $depth = 2;
        $category = $this->categoryRepositoryMock->get($rootCategoryId);

        $this->categoryRepositoryMock->expects($this->once())->method('get')->with($rootCategoryId);
        $this->categoryTreeMock->expects($this->once())->method('getRootNode')->with($category);
        $this->categoryTreeMock->expects($this->once())->method('getTree')->with($category, $depth);
        $this->model->getTree($rootCategoryId, $depth);
    }

    public function testGetTreeWithNullArguments()
    {
        $rootCategoryId = null;
        $depth = null;
        $category = $this->categoryRepositoryMock->get($rootCategoryId);

        $this->categoryTreeMock->expects($this->once())->method('getRootNode')->with($category)->willReturn(null);
        $this->categoryTreeMock->expects($this->once())->method('getTree')->with($category, $depth);
        $this->model->getTree($rootCategoryId, $depth);
    }

    public function testMove()
    {
        $categoryId = 2;
        $parentId = 1;
        $afterId = null;

        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false, true, true);

        $this->categoryRepositoryMock->expects($this->exactly(2))->method('get')->with($this->equalTo($categoryId) || $this->equalTo($parentId))->willReturn($categoryMock);
        $categoryMock->expects($this->once())->method('hasChildren')->willReturn(true);
        $categoryMock->expects($this->once())->method('getChildren');
        $categoryMock->expects($this->exactly(2))->method('getPath');
        $categoryMock->expects($this->exactly(1))->method('move')->with($parentId, $afterId);

        $this->assertTrue($this->model->move($categoryId, $parentId, $afterId));
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Operation do not allow to move a parent category to any of children category
     */
    public function testMoveWithException()
    {
        $categoryId = 2;
        $parentId = 1;
        $afterId = null;

        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false, true, true);
        $this->categoryRepositoryMock->expects($this->exactly(2))
            ->method('get')
            ->with($this->equalTo($categoryId) || $this->equalTo($parentId))
            ->willReturn($categoryMock);
        $categoryMock->expects($this->exactly(2))->method('getPath')->willReturn('test');
        $this->model->move($categoryId, $parentId, $afterId);
    }
    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Could not move category
     */
    public function testMoveWithCouldNotMoveException()
    {
        $categoryId = 2;
        $parentId = 1;
        $afterId = null;

        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false, true, true);
        $this->categoryRepositoryMock->expects($this->exactly(2))
            ->method('get')
            ->with($this->equalTo($categoryId) || $this->equalTo($parentId))
            ->willReturn($categoryMock);
        $categoryMock->expects($this->exactly(1))->method('move')->with($parentId, $afterId)->willThrowException(new \Magento\Framework\Model\Exception);
        $this->model->move($categoryId, $parentId, $afterId);
    }
}