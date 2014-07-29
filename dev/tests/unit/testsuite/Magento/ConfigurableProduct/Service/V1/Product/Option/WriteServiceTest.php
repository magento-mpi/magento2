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
                'storeManager' => $storeManagerMock
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
}
