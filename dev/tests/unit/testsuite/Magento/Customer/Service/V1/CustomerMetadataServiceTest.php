<?php
/**
 * \Magento\Customer\Service\Eav\CustomerMetadataService
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

use Magento\Framework\Exception\NoSuchEntityException;

class CustomerMetadataServiceTest extends \PHPUnit_Framework_TestCase
{
    /** Sample values for testing */
    const ATTRIBUTE_CODE = 1;

    const FRONTEND_INPUT = 'select';

    const INPUT_FILTER = 'input filter';

    const STORE_LABEL = 'store label';

    const FRONTEND_CLASS = 'frontend class';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Service\V1\Data\Eav\AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Service\V1\Data\Eav\AttributeMetadataConverter
     */
    protected $attributeMetadataConverter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    protected $attributeEntityMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata
     */
    protected $attributeMetadataMock;

    /** @var array */
    protected $validationRules = array();

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /** @var \Magento\Customer\Service\V1\CustomerMetadataServiceInterface */
    protected $service;

    public function setUp()
    {
        $this->attributeMetadataDataProvider = $this
            ->getMockBuilder('Magento\Customer\Service\V1\Data\Eav\AttributeMetadataDataProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $this->attributeMetadataConverter = $this
            ->getMockBuilder('Magento\Customer\Service\V1\Data\Eav\AttributeMetadataConverter')
            ->disableOriginalConstructor()
            ->getMock();

        $this->attributeMetadataMock = $this
            ->getMockBuilder('Magento\Customer\Service\V1\Data\Eav\AttributeMetadata')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'getAttributeCode',
                    'getFrontendInput',
                    'getInputFilter',
                    'getStoreLabel',
                    'getValidationRules',
                    'getSource',
                    'getFrontendClass',
                    'usesSource'
                )
            )
            ->getMock();

        $this->attributeEntityMock = $this->getMockBuilder(
            '\Magento\Eav\Model\Entity\Attribute\AbstractAttribute'
        )->setMethods(
            array(
                'getId',
                'getAttributeCode',
                'getFrontendInput',
                'getInputFilter',
                'getStoreLabel',
                'getValidateRules',
                'getSource',
                'getFrontend',
                'usesSource',
                '__wakeup'
            )
        )->disableOriginalConstructor()->getMock();

        $this->_mockReturnValue(
            $this->attributeMetadataMock,
            array(
                'getAttributeCode' => self::ATTRIBUTE_CODE,
                'getFrontendInput' => self::FRONTEND_INPUT,
                'getInputFilter' => self::INPUT_FILTER,
                'getStoreLabel' => self::STORE_LABEL,
                'getValidationRules' => $this->validationRules,
                'getFrontendClass' => self::FRONTEND_CLASS
            )
        );

        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->service = $this->objectManager->getObject(
            'Magento\Customer\Service\V1\CustomerMetadataService',
            [
                'attributeMetadataDataProvider' => $this->attributeMetadataDataProvider,
                'attributeMetadataConverter' => $this->attributeMetadataConverter
            ]
        );
    }

    public function testGetAttributeMetadata()
    {
        $this->attributeMetadataDataProvider
            ->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnValue($this->attributeEntityMock));

        $this->attributeEntityMock
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->attributeMetadataConverter
            ->expects($this->any())
            ->method('createMetadataAttribute')
            ->will($this->returnValue($this->attributeMetadataMock));

        $attributeMetadata = $this->service->getAttributeMetadata('attributeId');
        $this->assertMetadataAttributes($attributeMetadata);
    }

    public function testGetAttributeMetadataWithoutAttributeMetadata()
    {
        $this->attributeMetadataDataProvider
            ->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnValue(false));

        try {
            $this->service->getAttributeMetadata('attributeId');
            $this->fail('Expected exception not thrown.');
        } catch (NoSuchEntityException $e) {
            $this->assertSame(
                "No such entity with entityType = customer, attributeCode = attributeId",
                $e->getMessage()
            );
        }
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param array $valueMap
     */
    private function _mockReturnValue($mock, $valueMap)
    {
        foreach ($valueMap as $method => $value) {
            $mock->expects($this->any())->method($method)->will($this->returnValue($value));
        }
    }

    /**
     * @param $attributeMetadata
     */
    private function assertMetadataAttributes($attributeMetadata)
    {
        $this->assertEquals(self::ATTRIBUTE_CODE, $attributeMetadata->getAttributeCode());
        $this->assertEquals(self::FRONTEND_INPUT, $attributeMetadata->getFrontendInput());
        $this->assertEquals(self::INPUT_FILTER, $attributeMetadata->getInputFilter());
        $this->assertEquals(self::STORE_LABEL, $attributeMetadata->getStoreLabel());
        $this->assertEquals($this->validationRules, $attributeMetadata->getValidationRules());
        $this->assertEquals(self::FRONTEND_CLASS, $attributeMetadata->getFrontendClass());
    }
}
