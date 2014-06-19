<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\TestFramework\Helper\ObjectManager;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Service\V1\Category\ReadService
     */
    private $model;

    /**
     * @var \Magento\Catalog\Service\V1\Data\CategoryBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryBuilder;

    /**
     * @var \Magento\Catalog\Model\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    private $category;

    /**
     * @var \Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata
     */
    private $categoryMetadata;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->categoryMetadata = $this->getMockBuilder(
            'Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoryBuilder = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\CategoryBuilder')
            ->setMethods(['create', 'populateWithArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryBuilder->expects($this->any())->method('create')
            ->will($this->returnValue($this->categoryMetadata));

        $this->category = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->setMethods(['getData', 'getId', 'load', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Catalog\Model\CategoryFactory|\PHPUnit_Framework_MockObject_MockObject $categoryFactory */
        $categoryFactory = $this->getMockBuilder('Magento\Catalog\Model\CategoryFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $categoryFactory->expects($this->any())->method('create')
            ->will($this->returnValue($this->category));

        $this->model = $objectManager->getObject(
            'Magento\Catalog\Service\V1\Category\ReadService',
            ['categoryFactory' => $categoryFactory, 'categoryBuilder' => $this->categoryBuilder]
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testInfoNoSuchEntityException()
    {
        $id = 3;

        $this->category->expects($this->once())->method('load')->with($this->equalTo($id));
        $this->category->expects($this->once())->method('getId')->will($this->returnValue(false));

        $this->model->info($id);
    }

    public function testInfo()
    {
        $id = 3;
        $categoryData = [$id . 'someData'];

        $this->category->expects($this->once())->method('load')->with($this->equalTo($id));
        $this->category->expects($this->once())->method('getId')->will($this->returnValue(true));
        $this->category->expects($this->once())->method('getData')->will($this->returnValue($categoryData));

        $this->categoryBuilder->expects($this->once())->method('populateWithArray')
            ->with($this->equalTo($categoryData))
            ->will($this->returnValue($this->categoryBuilder));

        $this->assertInstanceOf(
            'Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata',
            $this->model->info($id)
        );
    }
}
