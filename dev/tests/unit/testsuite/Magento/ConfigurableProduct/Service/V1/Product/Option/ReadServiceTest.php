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
    const TYPE_FIELD_NAME = 'frontend_input';
    const ATTRIBUTE_ID_FIELD_NAME = 'product_super_attribute_id';
    const OPTION_TYPE = 'select';
    /**
     * @var \Magento\Catalog\Model\Resource\Eav\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eavAttribute;
    /**
     * @var \Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeResource;
    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Product\Option\ReadService
     */
    private $model;

    /**
     * @var \Magento\Catalog\Model\ProductRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepository;

    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Data\OptionConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionConverter;

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
     * @var \Magento\ConfigurableProduct\Service\V1\Data\Option|\PHPUnit_Framework_MockObject_MockObject
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
            ->setMethods(['__wakeup', 'getId', 'getProductAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->eavAttribute = $this->getMockBuilder('Magento\Catalog\Model\Resource\Eav\Attribute')
            ->setMethods(['getData', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->attributeResource = $this->getMockBuilder(
            'Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute'
        )
            ->setMethods(['getIdFieldName', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->configurableAttributeCollection = $this->getMockBuilder(
            'Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\Collection'
        )
            ->setMethods(['getResource', 'addFieldToFilter', 'getFirstItem'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->optionConverter = $this->getMockBuilder(
            'Magento\ConfigurableProduct\Service\V1\Data\OptionConverter'
        )
            ->setMethods(['convertFromModel'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadata = $this->getMockBuilder('\Magento\ConfigurableProduct\Service\V1\Data\Option')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            'Magento\ConfigurableProduct\Service\V1\Product\Option\ReadService',
            [
                'productRepository' => $this->productRepository,
                'optionConverter'   => $this->optionConverter
            ]
        );
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

        $this->optionConverter->expects($this->once())->method('convertFromModel')
            ->with($this->equalTo($this->option))
            ->will($this->returnValue($this->metadata));

        $this->assertEquals([$this->metadata], $this->model->getList($productSku));
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

        $this->configurableAttributeCollection->expects($this->once())
            ->method('addFieldToFilter')
            ->with(self::ATTRIBUTE_ID_FIELD_NAME, $optionId);

        $this->configurableAttributeCollection->expects($this->once())
            ->method('getFirstItem')
            ->will($this->returnValue($this->option));

        $this->option->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($optionId));

        $this->attributeResource->expects($this->once())
            ->method('getIdFieldName')
            ->will($this->returnValue(self::ATTRIBUTE_ID_FIELD_NAME));

        $this->configurableAttributeCollection->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($this->attributeResource));

        $this->optionConverter->expects($this->once())->method('convertFromModel')
            ->with($this->equalTo($this->option))
            ->will($this->returnValue($this->metadata));

        $this->assertEquals($this->metadata, $this->model->get($productSku, $optionId));
    }

    public function testGetType()
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

        $this->configurableAttributeCollection->expects($this->once())
            ->method('addFieldToFilter')
            ->with(self::ATTRIBUTE_ID_FIELD_NAME, $optionId);

        $this->configurableAttributeCollection->expects($this->once())
            ->method('getFirstItem')
            ->will($this->returnValue($this->option));

        $this->option->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($optionId));

        $this->attributeResource->expects($this->once())
            ->method('getIdFieldName')
            ->will($this->returnValue(self::ATTRIBUTE_ID_FIELD_NAME));

        $this->configurableAttributeCollection->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($this->attributeResource));

        $this->option->expects($this->once())
            ->method('getProductAttribute')
            ->will($this->returnValue($this->eavAttribute));

        $this->eavAttribute->expects($this->once())
            ->method('getData')
            ->with(self::TYPE_FIELD_NAME)
            ->will($this->returnValue(self::OPTION_TYPE));

        $this->assertEquals(self::OPTION_TYPE, $this->model->getType($productSku, $optionId));
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

        $this->configurableAttributeCollection->expects($this->once())
            ->method('addFieldToFilter')
            ->with(self::ATTRIBUTE_ID_FIELD_NAME, $optionId);

        $this->configurableAttributeCollection->expects($this->once())
            ->method('getFirstItem')
            ->will($this->returnValue($this->option));

        $this->option->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));

        $this->attributeResource->expects($this->once())
            ->method('getIdFieldName')
            ->will($this->returnValue(self::ATTRIBUTE_ID_FIELD_NAME));

        $this->configurableAttributeCollection->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($this->attributeResource));

        $this->model->get($productSku, $optionId);
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
