<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\TestFramework\Helper\ObjectManager;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Service\V1\Category\WriteService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $model;

    /**
     * @var \Magento\Catalog\Model\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    private $category;

    /**
     * @var \Magento\Catalog\Service\V1\Data\Category\Mapper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryMapper;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->category = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Catalog\Model\CategoryFactory|\PHPUnit_Framework_MockObject_MockObject $categoryFactory */
        $categoryFactory = $this->getMockBuilder('Magento\Catalog\Model\CategoryFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $categoryFactory->expects($this->any())->method('create')
            ->will($this->returnValue($this->category));

        $this->categoryMapper = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Category\Mapper')
            ->setMethods(['toModel'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            'Magento\Catalog\Service\V1\Category\WriteService',
            [
                'categoryFactory' => $categoryFactory,
                'categoryMapper' => $this->categoryMapper
            ]
        );
    }

    public function testCreate()
    {
        $categorySdo = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Category')
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoryMapper->expects($this->any())
            ->method('toModel')
            ->with($categorySdo)
            ->will($this->returnValue($this->category));

        $this->category->expects($this->once())->method('validate')->will($this->returnValue([]));
        $this->category->expects($this->once())->method('save');

        $this->model->create($categorySdo);
    }

    public function testDelete()
    {
        $id = 3;
        $this->category->expects($this->once())->method('getId')->will($this->returnValue($id));
        $this->category->expects($this->once())->method('delete');

        $this->assertTrue($this->model->delete($id));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDeleteNoSuchEntityException()
    {
        $this->model->delete(3);
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testDeleteCouldNotSaveException()
    {
        $id = 3;
        $this->category->expects($this->once())->method('getId')->will($this->returnValue($id));
        $this->category->expects($this->once())->method('delete')->will(
            $this->returnCallback(
                function () {
                    throw new \Exception();
                }
            )
        );

        $this->model->delete($id);
    }

    public function testUpdate()
    {
        $id = 3;
        $categorySdo = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Category')
            ->disableOriginalConstructor()
            ->getMock();

        $this->category->expects($this->once())->method('getId')->will($this->returnValue($id));
        $this->category->expects($this->once())->method('validate')->will($this->returnValue(true));
        $this->category->expects($this->once())->method('save');

        $this->assertTrue($this->model->update($id, $categorySdo));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testUpdateNoSuchEntityException()
    {
        $categorySdo = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Category')
            ->disableOriginalConstructor()
            ->getMock();
        $this->model->update(3, $categorySdo);
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testUpdateValidateException()
    {
        $id = 3;
        $categorySdo = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Category')
            ->disableOriginalConstructor()
            ->getMock();

        $this->category->expects($this->once())->method('getId')->will($this->returnValue($id));
        $this->category->expects($this->once())->method('validate')->will($this->returnValue(['Validation error']));

        $this->model->update($id, $categorySdo);
    }

    public function testMove()
    {
        $id = 5;
        $parentId = 2;
        $afterId = 3;

        $categoryFactory = $this->getMockBuilder('Magento\Catalog\Model\CategoryFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = (new ObjectManager($this))->getObject(
            'Magento\Catalog\Service\V1\Category\WriteService',
            ['categoryFactory' => $categoryFactory]
        );

        $category = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()
            ->getMock();
        $category->expects($this->once())->method('getId')->will($this->returnValue($id));
        $category->expects($this->any())->method('__call')->with('getPath')->will($this->returnValue('1\2\3\4'));
        $category->expects($this->once())->method('move')->with($parentId, $afterId);

        $parentCategory = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()
            ->getMock();
        $parentCategory->expects($this->once())->method('getId')->will($this->returnValue($parentId));
        $parentCategory->expects($this->any())->method('__call')->with('getPath')->will($this->returnValue('1\2'));

        $categoryFactory->expects($this->at(0))->method('create')
            ->will($this->returnValue($category));

        $categoryFactory->expects($this->at(1))->method('create')
            ->will($this->returnValue($parentCategory));

        $this->assertTrue($this->model->move($id, $parentId, $afterId));
    }

    public function testMoveAfterId()
    {
        $id = 5;
        $parentId = 2;
        $afterId = 3;

        $categoryFactory = $this->getMockBuilder('Magento\Catalog\Model\CategoryFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = (new ObjectManager($this))->getObject(
            'Magento\Catalog\Service\V1\Category\WriteService',
            ['categoryFactory' => $categoryFactory]
        );

        $category = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()
            ->getMock();
        $category->expects($this->once())->method('getId')->will($this->returnValue($id));
        $category->expects($this->any())->method('__call')->with('getPath')->will($this->returnValue('1\2\3\4'));
        $category->expects($this->once())->method('move')->with($parentId, $afterId);

        $parentCategory = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()
            ->getMock();
        $parentCategory->expects($this->once())->method('getId')->will($this->returnValue($parentId));
        $parentCategory->expects($this->any())->method('__call')->with('getPath')->will($this->returnValue('1\2'));
        $parentCategory->expects($this->once())->method('hasChildren')->will($this->returnValue(true));
        $parentCategory->expects($this->once())->method('getChildren')->will($this->returnValue('6,7,' . $afterId));

        $categoryFactory->expects($this->at(0))->method('create')
            ->will($this->returnValue($category));

        $categoryFactory->expects($this->at(1))->method('create')
            ->will($this->returnValue($parentCategory));

        $this->assertTrue($this->model->move($id, $parentId, null));
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     */
    public function testMoveParentToChild()
    {
        $id = 5;
        $parentId = 2;
        $afterId = 3;

        $categoryFactory = $this->getMockBuilder('Magento\Catalog\Model\CategoryFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = (new ObjectManager($this))->getObject(
            'Magento\Catalog\Service\V1\Category\WriteService',
            ['categoryFactory' => $categoryFactory]
        );

        $category = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()
            ->getMock();
        $category->expects($this->once())->method('getId')->will($this->returnValue($id));
        $category->expects($this->any())->method('__call')->with('getPath')->will($this->returnValue('1\2\3'));

        $parentCategory = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()
            ->getMock();
        $parentCategory->expects($this->once())->method('getId')->will($this->returnValue($parentId));
        $parentCategory->expects($this->any())->method('__call')->with('getPath')->will($this->returnValue('1\2\3\4'));

        $categoryFactory->expects($this->at(0))->method('create')
            ->will($this->returnValue($category));

        $categoryFactory->expects($this->at(1))->method('create')
            ->will($this->returnValue($parentCategory));

        $this->assertTrue($this->model->move($id, $parentId, $afterId));
    }
}
