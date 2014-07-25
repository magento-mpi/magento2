<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Option;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\TestFramework\Helper\ObjectManager;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Product\Option\ReadService
     */
    private $model;

    /**
     * @var \Magento\Catalog\Model\ProductRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepository;

    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Data\ConfigurableAttributeConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configurableAttributeConverter;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Interceptor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productType;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $product;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    private $option;

    /**
     * @var \Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configurableAttributeCollection;

    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Data\ConfigurableAttribute|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadata;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->productRepository = $this->getMockBuilder('Magento\Catalog\Model\ProductRepository')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->productType = $this->getMockBuilder(
            'Magento\ConfigurableProduct\Model\Product\Type\Configurable\Interceptor'
        )
            ->setMethods(['getConfigurableAttributeCollection'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['__wakeup', 'getTypeId', 'getTypeInstance'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->product->expects($this->any())->method('getTypeInstance')->will($this->returnValue($this->productType));

        $this->option = $this->getMockBuilder('Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute')
            ->setMethods(['__wakeup', 'getItemId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->configurableAttributeCollection = $this->getMockBuilder(
            'Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\Collection'
        )
            ->setMethods(['getItemById'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->configurableAttributeConverter = $this->getMockBuilder(
            'Magento\ConfigurableProduct\Service\V1\Data\ConfigurableAttributeConverter'
        )
            ->setMethods(['createDataFromModel'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadata = $this->getMockBuilder('\Magento\ConfigurableProduct\Service\V1\Data\ConfigurableAttribute')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            'Magento\ConfigurableProduct\Service\V1\Product\Option\ReadService',
            [
                'productRepository'              => $this->productRepository,
                'configurableAttributeConverter' => $this->configurableAttributeConverter
            ]
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
            ->will($this->returnValue(ConfigurableType::TYPE_CODE));

        $this->productType->expects($this->once())->method('getConfigurableAttributeCollection')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue($this->configurableAttributeCollection));

        $this->configurableAttributeCollection->expects($this->once())->method('getItemById')
            ->with($this->equalTo($optionId))
            ->will($this->returnValue($this->option));

        $this->configurableAttributeConverter->expects($this->once())->method('createDataFromModel')
            ->with($this->equalTo($this->option))
            ->will($this->returnValue($this->metadata));

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
            ->will($this->returnValue(ConfigurableType::TYPE_CODE));

        $this->productType->expects($this->once())->method('getConfigurableAttributeCollection')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue($this->configurableAttributeCollection));

        $this->configurableAttributeCollection->expects($this->once())->method('getItemById')
            ->with($this->equalTo($optionId))
            ->will($this->returnValue(null));


        $this->model->get($productSku, $optionId);
    }

    public function testGetList()
    {
        $productSku = 'oneSku';

        $this->productRepository->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->product));

        $this->product->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(ConfigurableType::TYPE_CODE));

        $this->productType->expects($this->once())->method('getConfigurableAttributeCollection')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue([$this->option]));

        $this->configurableAttributeConverter->expects($this->once())->method('createDataFromModel')
            ->with($this->equalTo($this->option))
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
