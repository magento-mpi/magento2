<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Option;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Catalog\Model\ProductRepository;
use Magento\ConfigurableProduct\Service\V1\Data\Option;
use Magento\ConfigurableProduct\Service\V1\Data\OptionBuilder;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory as ConfigurableAttributeFactory;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectManager */
    protected $objectManager;

    /**
     * @var ProductRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepositoryMock;

    /**
     * @var ConfigurableAttributeFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $confAttributeFactoryMock;

    /**
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavConfigMock;

    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Data\OptionBuilder
     */
    protected $optionBuilder;

    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Product\Option\WriteService
     */
    protected $writeService;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productMock;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productTypeMock;

    /**
     * @var \Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeCollectionMock;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeMock;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->productRepositoryMock = $this->getMockBuilder('Magento\Catalog\Model\ProductRepository')
            ->disableOriginalConstructor()->setMethods(['get'])->getMock();

        $this->confAttributeFactoryMock = $this
            ->getMockBuilder('Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory')
            ->disableOriginalConstructor()->setMethods(['create'])->getMock();

        $this->eavConfigMock = $this
            ->getMockBuilder('Magento\Eav\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getSku', 'getTypeId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->productTypeMock = $this->getMockBuilder('Magento\ConfigurableProduct\Model\Product\Type\Configurable')
            ->setMethods(['getConfigurableAttributeCollection'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->attributeCollectionMock = $this->getMockBuilder(
            'Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\Collection'
        )
            ->setMethods(['getItemById'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->attributeMock = $this->getMockBuilder(
            'Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute'
        )
            ->setMethods(['delete', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $storeManagerMock = $this->getMock('Magento\Store\Model\StoreManagerInterface', [], [], '', false);
        $storeManagerMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue(new \Magento\Framework\Object()));

        $this->writeService = $this->objectManager->getObject(
            'Magento\ConfigurableProduct\Service\V1\Product\Option\WriteService',
            [
                'productRepository' => $this->productRepositoryMock,
                'configurableAttributeFactory' => $this->confAttributeFactoryMock,
                'eavConfig' => $this->eavConfigMock,
                'storeManager' => $storeManagerMock,
                'productType' => $this->productTypeMock
            ]
        );

        $this->optionBuilder = $this->objectManager
            ->getObject('Magento\ConfigurableProduct\Service\V1\Data\OptionBuilder');
    }

    /**
     * Add configurable option test
     */
    public function testAdd()
    {
        $productSku = 'test_sku';
        $option = $this->getOption();

        $productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['save', 'setConfigurableAttributesData', 'setStoreId', 'getTypeId', 'setTypeId', '__sleep', '__wakeup'],
            [], '', false
        );
        $productMock->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue(ProductType::TYPE_SIMPLE));
        $this->productRepositoryMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($productMock));

        $confAttributeMock = $this->getMock(
            'Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute', [], [], '', false
        );
        $this->confAttributeFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($confAttributeMock));

        $confAttributeMock->expects($this->exactly(2))->method('loadByProductAndAttribute');
        $confAttributeMock->expects($this->at(1))->method('getId')->will($this->returnValue(null));
        $confAttributeMock->expects($this->at(3))->method('getId')->will($this->returnValue(1));

        $productMock->expects($this->once())->method('setTypeId')->with(ConfigurableType::TYPE_CODE);
        $productMock->expects($this->once())->method('setConfigurableAttributesData');
        $productMock->expects($this->once())->method('setStoreId')->with(0);
        $productMock->expects($this->once())->method('save');

        $this->writeService->add($productSku, $option);
    }

    /**
     * Invalid product type check
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAddInvalidProductType()
    {
        $productSku = 'test_sku';
        $option = $this->getOption();

        $productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $productMock->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue(ProductType::TYPE_BUNDLE));
        $this->productRepositoryMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($productMock));

        $this->writeService->add($productSku, $option);
    }

    /**
     * Return instance of option for configurable product
     *
     * @return \Magento\Framework\Service\Data\AbstractObject
     */
    private function getOption()
    {
        $data = [
            Option::ID => 1,
            Option::ATTRIBUTE_ID => 2,
            Option::LABEL => 'Test Label',
            Option::POSITION => 1,
            Option::USE_DEFAULT => true,
            Option::VALUES => [
                [
                    'index' => 1,
                    'price' => 12,
                    'percent' => true
                ]
            ]
        ];

        return $this->optionBuilder->populateWithArray($data)->create();
    }

    public function testRemove()
    {
        $productSku = 'productSku';
        $optionId = 3;

        $this->productRepositoryMock->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->productMock));

        $this->productMock->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(ConfigurableType::TYPE_CODE));

        $this->productTypeMock->expects($this->once())->method('getConfigurableAttributeCollection')
            ->with($this->equalTo($this->productMock))
            ->will($this->returnValue($this->attributeCollectionMock));

        $this->attributeCollectionMock->expects($this->once())->method('getItemById')
            ->with($this->equalTo($optionId))
            ->will($this->returnValue($this->attributeMock));

        $this->attributeMock->expects($this->once())->method('delete');

        $this->assertTrue($this->writeService->remove($productSku, $optionId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRemoveNoSuchEntityException()
    {
        $productSku = 'productSku';
        $optionId = 3;

        $this->productRepositoryMock->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->productMock));

        $this->productMock->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(ConfigurableType::TYPE_CODE));

        $this->productTypeMock->expects($this->once())->method('getConfigurableAttributeCollection')
            ->with($this->equalTo($this->productMock))
            ->will($this->returnValue($this->attributeCollectionMock));

        $this->attributeCollectionMock->expects($this->once())->method('getItemById')
            ->with($this->equalTo($optionId))
            ->will($this->returnValue(null));

        $this->writeService->remove($productSku, $optionId);
    }

    /**
     * @expectedException \Magento\Webapi\Exception
     */
    public function testRemoveWebApiException()
    {
        $productSku = 'productSku';

        $this->productRepositoryMock->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->productMock));

        $this->productMock->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(ProductType::TYPE_SIMPLE));
        $this->productMock->expects($this->once())->method('getSku')
            ->will($this->returnValue($productSku));

        $this->writeService->remove($productSku, 3);
    }
}
