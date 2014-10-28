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
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $model;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryFactoryMock;

    /**
     * @var  \Magento\Catalog\Model\Resource\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryResourceMock;

    /**
     * @var \Magento\Framework\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    protected function setUp()
    {
        $this->categoryFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\CategoryFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->categoryResourceMock = $this->getMock('\Magento\Catalog\Model\Resource\Category', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');

        $this->model = new \Magento\Catalog\Model\CategoryRepository(
            $this->categoryFactoryMock,
            $this->categoryResourceMock,
            $this->storeManagerMock
        );
    }

    public function testGet()
    {
        $categoryId = 5;
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', [], [], '', false);
        $categoryMock->expects(
            $this->once()
        )->method('getId')->willReturn(
            $categoryId
        );
        $this->categoryFactoryMock->expects(
            $this->once()
        )->method('create')->willReturn(
            $categoryMock
        );
        $this->categoryResourceMock->expects(
            $this->once()
        )->method('load')->with(
            $categoryMock,
            $categoryId
        );

        $this->assertEquals($categoryMock, $this->model->get($categoryId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 5
     */
    public function testGetWhenCategoryDoesNotExist()
    {
        $categoryId = 5;
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', [], [], '', false);
        $categoryMock->expects(
            $this->once()
        )->method('getId')->willReturn(null);
        $this->categoryFactoryMock->expects(
            $this->once()
        )->method('create')->willReturn(
            $categoryMock
        );
        $this->categoryResourceMock->expects(
            $this->once()
        )->method('load')->with(
            $categoryMock,
            $categoryId
        );

        $this->assertEquals($categoryMock, $this->model->get($categoryId));
    }

    public function testSave()
    {
        $categoryId = 5;
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', [], [], '', false, true, true);
        $categoryMock->expects(
            $this->atLeastOnce()
        )->method('getId')->willReturn($categoryId);
        $this->categoryFactoryMock->expects(
            $this->once()
        )->method('create')->willReturn(
            $categoryMock
        );
        $categoryMock->expects($this->once())->method('validate')->willReturn(true);
        $categoryMock->expects($this->once())->method('getParentId')->willReturn(3);
        $this->categoryResourceMock->expects($this->once())->method('save')->willReturn('\Magento\Framework\Object');
        $this->assertEquals($categoryId, $this->model->save($categoryMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Could not save category
     */
    public function testSaveWithException()
    {
        $categoryId = 5;
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', [], [], '', false, true, true);
        $categoryMock->expects(
            $this->atLeastOnce()
        )->method('getId')->willReturn($categoryId);
        $this->categoryFactoryMock->expects(
            $this->once()
        )->method('create')->willReturn(
            $categoryMock
        );
        $categoryMock->expects($this->once())->method('validate')->willReturn(false);
        $categoryMock->expects($this->once())->method('getParentId')->willReturn(3);
        $this->model->save($categoryMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Could not save category: Attribute "ValidateCategoryTest" is required.
     */
    public function testSaveWithValidateCategoryException()
    {
        $categoryId = 5;
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', [], [], '', false);
        $objectMock = $this->getMock('\Magento\Framework\Object', ['getFrontend', 'getLabel'], [], '', false);
        $categoryMock->expects(
            $this->atLeastOnce()
        )->method('getId')->willReturn($categoryId);
        $this->categoryFactoryMock->expects(
            $this->once()
        )->method('create')->willReturn(
            $categoryMock
        );
        $objectMock->expects($this->any())->method('getFrontend')->willReturn($objectMock);
        $objectMock->expects($this->any())->method('getLabel')->willReturn('ValidateCategoryTest');
        $categoryMock->expects($this->once())->method('getParentId')->willReturn(3);
        $categoryMock->expects($this->once())->method('validate')->willReturn([42 => true]);
        $this->categoryResourceMock->expects($this->once())->method('getAttribute')->with(42)->willReturn($objectMock);
        $categoryMock->expects($this->never())->method('unsetData');
        $this->model->save($categoryMock);
    }

    public function testDelete()
    {
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', [], [], '', false, true, true);
        $this->assertTrue($this->model->delete($categoryMock));
    }

    /**
     * @throws \Magento\Framework\Exception\StateException
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Cannot delete category with id
     */
    public function testDeleteWithException()
    {
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', [], [], '', false, true, true);
        $this->categoryResourceMock->expects($this->once())->method('delete')->willThrowException(new \Exception());
        $this->model->delete($categoryMock);
    }

    public function testDeleteByIdentifier()
    {
        $categoryId = 5;
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', [], [], '', false);
        $categoryMock->expects(
            $this->once()
        )->method('getId')->willReturn(
            $categoryId
        );
        $this->categoryFactoryMock->expects(
            $this->once()
        )->method('create')->willReturn(
            $categoryMock
        );
        $this->categoryResourceMock->expects(
            $this->once()
        )->method('load')->with(
            $categoryMock,
            $categoryId
        );
        $this->assertTrue($this->model->deleteByIdentifier($categoryId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 5
     */
    public function testDeleteByIdentifierWithException()
    {
        $categoryId = 5;
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', [], [], '', false);
        $categoryMock->expects(
            $this->once()
        )->method('getId')->willReturn(null);
        $this->categoryFactoryMock->expects(
            $this->once()
        )->method('create')->willReturn(
            $categoryMock
        );
        $this->categoryResourceMock->expects(
            $this->once()
        )->method('load')->with(
            $categoryMock,
            $categoryId
        );
        $this->model->deleteByIdentifier($categoryId);
    }
}
