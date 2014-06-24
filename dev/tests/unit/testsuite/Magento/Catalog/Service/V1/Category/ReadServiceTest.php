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
use Magento\Catalog\Service\V1\Data\Eav\Category\ProductConverter;
use Magento\Catalog\Service\V1\Data\Eav\Category\ProductConverterFactory;
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

    /**
     * @var ProductConverterFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productConverterFactory;

    /**
     * @var ProductConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productConverter;

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

        $this->productConverter = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Eav\Category\ProductConverter')
            ->setMethods(['createProductDataFromModel', 'setPosition'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->productConverterFactory = $this->getMockBuilder(
            '\Magento\Catalog\Service\V1\Data\Eav\Category\ProductConverterFactory'
        )->setMethods(['create'])->disableOriginalConstructor()->getMock();
        $this->productConverterFactory->expects($this->any())->method('create')
            ->will($this->returnValue($this->productConverter));

        $this->model = $objectManager->getObject(
            'Magento\Catalog\Service\V1\Category\ReadService',
            [
                'categoryFactory' => $categoryFactory,
                'builder' => $this->categoryBuilder,
                'converterFactory' => $this->converterFactory,
                'productConverterFactory' => $this->productConverterFactory
            ]
        );

        $this->converter->expects($this->any())->method('createDataFromModel')
            ->with($this->identicalTo($this->category))
            ->will($this->returnValue($this->categoryInfoMetadata));
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

    public function testAssignedProducts()
    {
        $categoryId = 3;
        $productPosition = 1;
        $productId = $categoryId + 6;

        $productDto = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Eav\Category\Product')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Catalog\Model\Product $productObject */
        $productObject = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Framework\Data\Collection\Db|\PHPUnit_Framework_MockObject_MockObject $productCollection */
        $productCollection = $this->getMockBuilder('Magento\Framework\Data\Collection\Db')
            ->disableOriginalConstructor()
            ->getMock();
        $productCollection->expects($this->any())->method('getItems')
            ->will($this->returnValue([$productId => $productObject]));

        $this->category->expects($this->once())->method('load')->with($this->equalTo($categoryId));
        $this->category->expects($this->once())->method('getId')->will($this->returnValue(true));
        $this->category->expects($this->once())->method('getProductsPosition')
            ->will($this->returnValue([$productId => $productPosition]));
        $this->category->expects($this->once())->method('getProductCollection')
            ->will($this->returnValue($productCollection));

        $this->productConverter->expects($this->once())->method('setPosition')->with($this->equalTo($productPosition));
        $this->productConverter->expects($this->once())->method('createProductDataFromModel')
            ->with($this->equalTo($productObject))
            ->will($this->returnValue($productDto));

        $this->assertEquals([$productDto], $this->model->assignedProducts($categoryId));
    }
}
