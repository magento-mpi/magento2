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
        $this->category->expects($this->once())->method('load');
        $this->category->expects($this->exactly(2))->method('getId')->will($this->returnValue($id));
        $this->category->expects($this->once())->method('addData');
        $this->category->expects($this->once())->method('validate');
        $this->category->expects($this->once())->method('save');

        $data = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $data->expects($this->once())->method('__toArray')->will($this->returnValue(array()));

        $this->assertEquals($id, $this->model->update($id, $data));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testUpdateNoSuchEntityException()
    {
        $data = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $this->model->update(3, $data);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testUpdateInputException()
    {
        $id = 3;
        $this->category->expects($this->once())->method('load');
        $this->category->expects($this->once())->method('getId')->will($this->returnValue($id));
        $this->category->expects($this->once())->method('addData');
        $this->category
            ->expects($this->once())
            ->method('validate')
            ->will($this->throwException(new \Magento\Eav\Model\Entity\Attribute\Exception()));

        $data = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $data->expects($this->once())->method('__toArray')->will($this->returnValue(array()));

        $this->assertEquals($id, $this->model->update($id, $data));
    }
}
