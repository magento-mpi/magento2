<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option;

use Magento\TestFramework\Helper\ObjectManager;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Service\V1\Product\Option\ReadService
     */
    private $model;

    /**
     * @var \Magento\Catalog\Model\ProductRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepository;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Option\MetadataConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataConverter;

    /**
     * @var \Magento\Bundle\Model\Product\Type|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productType;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $product;

    /**
     * @var \Magento\Bundle\Model\Option|\PHPUnit_Framework_MockObject_MockObject
     */
    private $option;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Option\Metadata|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadata;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->productRepository = $this->getMockBuilder('Magento\Catalog\Model\ProductRepository')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->productType= $this->getMockBuilder('Magento\Bundle\Model\Product\Type')
            ->setMethods(['getOptionsCollection'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['__wakeup', 'getTypeId', 'getTypeInstance'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->product->expects($this->any())->method('getTypeInstance')->will($this->returnValue($this->productType));

        $this->option = $this->getMockBuilder('Magento\Bundle\Model\Option')
            ->setMethods(['__wakeup', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadataConverter = $this->getMockBuilder('\Magento\Bundle\Service\V1\Data\Option\MetadataConverter')
            ->setMethods(['createDataFromModel'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadata = $this->getMockBuilder('Magento\Bundle\Service\V1\Data\Option\Metadata')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            'Magento\Bundle\Service\V1\Product\Option\ReadService',
            ['metadataConverter' => $this->metadataConverter, 'productRepository' => $this->productRepository]
        );
    }

    public function testGet()
    {
        $productSku = 'oneSku';
        $optionId = 3;

        $this->productRepository->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->product));

        $this->product->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE));

        $this->productType->expects($this->once())->method('getOptionsCollection')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue([$this->option]));

        $this->metadataConverter->expects($this->once())->method('createDataFromModel')
            ->with($this->equalTo($this->option), $this->equalTo($this->product))
            ->will($this->returnValue($this->metadata));

        $this->option->expects($this->once())->method('getId')->will($this->returnValue($optionId));

        $this->assertEquals($this->metadata, $this->model->get($productSku, $optionId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetNoSuchEntityException()
    {
        $productSku = 'oneSku';
        $optionId = 3;

        $this->productRepository->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->product));

        $this->product->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE));

        $this->productType->expects($this->once())->method('getOptionsCollection')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue([$this->option]));

        $this->option->expects($this->once())->method('getId')->will($this->returnValue($optionId + 1));

        $this->model->get($productSku, $optionId);
    }

    public function testGetList()
    {
        $productSku = 'oneSku';

        $this->productRepository->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->product));

        $this->product->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE));

        $this->productType->expects($this->once())->method('getOptionsCollection')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue([$this->option]));

        $this->metadataConverter->expects($this->once())->method('createDataFromModel')
            ->with($this->equalTo($this->option), $this->equalTo($this->product))
            ->will($this->returnValue($this->metadata));

        $this->assertEquals([$this->metadata], $this->model->getList($productSku));
    }

    /**
     * @expectedException \Magento\Webapi\Exception
     * @expectedExceptionCode 403
     */
    public function testGetListWebApiException()
    {
        $productSku = 'oneSku';

        $this->productRepository->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->product));

        $this->product->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE));

        $this->model->getList($productSku);
    }
}
