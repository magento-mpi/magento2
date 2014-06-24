<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Service\V1\Data\Eav\Category\Info\Converter;
use Magento\Catalog\Service\V1\Data\Eav\Category\Info\ConverterFactory;
use Magento\Catalog\Service\V1\Data\Eav\Category\Info\Metadata;
use Magento\Catalog\Service\V1\Data\Eav\Category\Info\MetadataBuilder;
use Magento\TestFramework\Helper\ObjectManager;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Service\V1\Category\ReadService
     */
    private $model;

    /**
     * @var MetadataBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryBuilder;

    /**
     * @var Category|\PHPUnit_Framework_MockObject_MockObject
     */
    private $category;

    /**
     * @var Metadata|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryInfoMetadata;

    /**
     * @var ConverterFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $converterFactory;

    /**
     * @var Converter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $converter;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->categoryInfoMetadata = $this->getMockBuilder(
            'Magento\Catalog\Service\V1\Data\Eav\Category\Info\Metadata'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoryBuilder = $this->getMockBuilder(
            'Magento\Catalog\Service\V1\Data\Eav\Category\Info\MetadataBuilder'
        )
            ->setMethods(['create', 'populateWithArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryBuilder->expects($this->any())->method('create')
            ->will($this->returnValue($this->categoryInfoMetadata));

        $this->category = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->setMethods(['getData', 'getId', 'load', '__wakeup', 'getProductsPosition', 'getProductCollection'])
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Catalog\Model\CategoryFactory|\PHPUnit_Framework_MockObject_MockObject $categoryFactory */
        $categoryFactory = $this->getMockBuilder('Magento\Catalog\Model\CategoryFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $categoryFactory->expects($this->any())->method('create')
            ->will($this->returnValue($this->category));

        $this->converter = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Eav\Category\Info\Converter')
            ->setMethods(['createDataFromModel'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->converterFactory = $this->getMockBuilder(
            'Magento\Catalog\Service\V1\Data\Eav\Category\Info\ConverterFactory'
        )
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->converterFactory->expects($this->any())->method('create')->will($this->returnValue($this->converter));

        $this->converter->expects($this->any())->method('createDataFromModel')
            ->with($this->identicalTo($this->category))
            ->will($this->returnValue($this->categoryInfoMetadata));

        $this->model = $objectManager->getObject(
            'Magento\Catalog\Service\V1\Category\ReadService',
            [
                'categoryFactory' => $categoryFactory,
                'builder' => $this->categoryBuilder,
                'converterFactory' => $this->converterFactory
            ]
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
        $this->category->expects($this->once())->method('load')->with($this->equalTo($id));
        $this->category->expects($this->once())->method('getId')->will($this->returnValue(true));

        $this->assertInstanceOf(
            'Magento\Catalog\Service\V1\Data\Eav\Category\Info\Metadata',
            $this->model->info($id)
        );
    }
}
