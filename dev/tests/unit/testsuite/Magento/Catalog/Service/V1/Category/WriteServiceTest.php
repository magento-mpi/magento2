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
     * @var \Magento\Catalog\Service\V1\Data\CategoryMapper
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

        $this->categoryMapper = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\CategoryMapper')
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
}
