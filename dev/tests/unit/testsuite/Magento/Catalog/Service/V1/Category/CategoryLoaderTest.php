<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\TestFramework\Helper\ObjectManager;

class CategoryLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Service\V1\Category\CategoryLoader
     */
    private $model;

    /**
     * @var \Magento\Catalog\Model\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    private $category;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->category = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->setMethods(['load', 'getId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Catalog\Model\CategoryFactory|\PHPUnit_Framework_MockObject_MockObject $categoryFactory */
        $categoryFactory = $this->getMockBuilder('Magento\Catalog\Model\CategoryFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $categoryFactory->expects($this->any())->method('create')->will($this->returnValue($this->category));

        $this->model = $objectManager->getObject(
            'Magento\Catalog\Service\V1\Category\CategoryLoader',
            [
                'categoryFactory' => $categoryFactory,
            ]
        );
    }

    public function testLoad()
    {
        $categoryId = 333;
        $this->category->expects($this->once())->method('load')->with($this->equalTo($categoryId));
        $this->category->expects($this->once())->method('getId')->will($this->returnValue($categoryId));

        $this->assertInstanceOf('Magento\Catalog\Model\Category', $this->model->load($categoryId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testLoadNoSuchEntityException()
    {
        $categoryId = 333;
        $this->category->expects($this->once())->method('load')->with($this->equalTo($categoryId));
        $this->category->expects($this->once())->method('getId')->will($this->returnValue(false));

        $this->model->load($categoryId);
    }
}
