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
            false);
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
        $categoryMock = $this->getMock('Magento\Catalog\Api\Data\CategoryInterface');
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
            $categoryMock, $categoryId
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
        $categoryMock = $this->getMock('Magento\Catalog\Api\Data\CategoryInterface');
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
            $categoryMock, $categoryId
        );

        $this->assertEquals($categoryMock, $this->model->get($categoryId));
    }

    public function testSave()
    {
        $categoryId = 5;
        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false, true, true);
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
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveWithException()
    {
        $categoryId = 5;
        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false, true, true);
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
        $this->assertEquals($categoryId, $this->model->save($categoryMock));
    }

    public function testDelete()
    {
        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false, true, true);
        $this->assertTrue($this->model->delete($categoryMock));
    }

    /**
     * @throws \Magento\Framework\Exception\StateException
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Cannot delete category with id
     */
    public function testDeleteWithException()
    {
        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false, true, true);
        $this->categoryResourceMock->expects($this->once())->method('delete')->willThrowException(new \Exception());
        $this->model->delete($categoryMock);
    }

    public function testDeleteByIdentifier()
    {
        $categoryId = 5;
        $categoryMock = $this->getMock('Magento\Catalog\Api\Data\CategoryInterface');
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
            $categoryMock, $categoryId
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
        $categoryMock = $this->getMock('Magento\Catalog\Api\Data\CategoryInterface');
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
            $categoryMock, $categoryId
        );
        $this->model->deleteByIdentifier($categoryId);
    }

    public function testValidateCategory()
    {
        $reflection = new \ReflectionClass(get_class($this->model));
        $method = $reflection->getMethod('validateCategory');
        $method->setAccessible(true);

        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false, true, true);
        $categoryMock->expects($this->atLeastOnce())->method('getData')->willReturn(true);
        $categoryMock->expects($this->once())->method('setData')->with('use_post_data_config', []);
        $categoryMock->expects($this->once())->method('validate')->willReturn(true);
        $categoryMock->expects($this->once())->method('unsetData')->with('use_post_data_config');

        $method->invokeArgs($this->model, [$categoryMock]);
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Something went wrong.
     */
    public function testValidateCategoryWithException()
    {
        $reflection = new \ReflectionClass(get_class($this->model));
        $method = $reflection->getMethod('validateCategory');
        $method->setAccessible(true);

        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false, true, true);
        $categoryMock->expects($this->atLeastOnce())->method('getData')->willReturn(true);
        $categoryMock->expects($this->once())->method('setData')->with('use_post_data_config', []);
        $categoryMock->expects($this->once())->method('validate')->willReturn([0 => 'Something went wrong.']);
        $categoryMock->expects($this->never())->method('unsetData');

        $method->invokeArgs($this->model, [$categoryMock]);
    }
}