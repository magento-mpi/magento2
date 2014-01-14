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

class CustomerMetadataServiceTest extends \PHPUnit_Framework_TestCase
{
    /** Sample values for testing */
    const ATTRIBUTE_CODE = 1;
    const FRONTEND_INPUT = 'frontend input';
    const INPUT_FILTER = 'input filter';
    const STORE_LABEL = 'store label';
    const VALIDATE_RULES = 'validate rules';
    const FRONTEND_CLASS = 'frontend class';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Eav\Model\Config
     */
    private $_eavConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    private $_attributeEntityMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
     */
    private $_sourceMock;

    public function setUp()
    {
        $this->_eavConfigMock = $this->getMockBuilder('\Magento\Eav\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_attributeEntityMock =
            $this->getMockBuilder('\Magento\Eav\Model\Entity\Attribute\AbstractAttribute')
                ->setMethods(
                    array(
                        'getAttributeCode',
                        'getFrontendInput',
                        'getInputFilter',
                        'getStoreLabel',
                        'getValidateRules',
                        'getSource',
                        'getFrontend',
                        '__wakeup',
                    )
                )
                ->disableOriginalConstructor()
                ->getMock();

        $this->_sourceMock =
            $this->getMockBuilder('\Magento\Eav\Model\Entity\Attribute\Source\AbstractSource')
                ->disableOriginalConstructor()
                ->getMock();

        $frontendMock = $this->getMockBuilder('\Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend')
            ->disableOriginalConstructor()
            ->setMethods(array('getClass'))
            ->getMock();

        $frontendMock
            ->expects($this->any())
            ->method('getClass')
            ->will($this->returnValue(self::FRONTEND_CLASS));

        $this->_mockReturnValue(
            $this->_attributeEntityMock,
            array(
                'getAttributeCode' => self::ATTRIBUTE_CODE,
                'getFrontendInput' => self::FRONTEND_INPUT,
                'getInputFilter' => self::INPUT_FILTER,
                'getStoreLabel' => self::STORE_LABEL,
                'getValidateRules' => self::VALIDATE_RULES,
                'getFrontend' => $frontendMock
            )
        );
    }

    public function testGetAttributeMetadata()
    {
        $this->_eavConfigMock->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnValue($this->_attributeEntityMock));

        $this->_attributeEntityMock->expects($this->any())
            ->method('getSource')
            ->will($this->returnValue($this->_sourceMock));

        $allOptions = array(
            array(
                'label' => 'label1',
                'value' => 'value1',
            ),
            array(
                'label' => 'label2',
                'value' => 'value2',
            ),
        );
        $this->_sourceMock->expects($this->any())
            ->method('getAllOptions')
            ->will($this->returnValue($allOptions));

        $attributeColMock = $this->getMockBuilder('\\Magento\\Customer\\Model\\Resource\\Form\\Attribute\\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $storeManagerMock = $this->getMockBuilder('\\Magento\\Core\\Model\\StoreManager')
            ->disableOriginalConstructor()
            ->getMock();

        $optionBuilder = new \Magento\Customer\Service\V1\Dto\Eav\OptionBuilder();

        $attributeMetadataBuilder = new \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadataBuilder();

        $service = new CustomerMetadataService($this->_eavConfigMock, $attributeColMock, $storeManagerMock,
            $optionBuilder, $attributeMetadataBuilder);

        $attributeMetadata = $service->getAttributeMetadata('entityCode', 'attributeId');
        $this->assertMetadataAttributes($attributeMetadata);

        $options = $attributeMetadata->getOptions();
        $this->assertNotEquals(array(), $options);
        $this->assertEquals('label1', $options['label1']->getLabel());
        $this->assertEquals('value1', $options['label1']->getValue());
        $this->assertEquals('label2', $options['label2']->getLabel());
        $this->assertEquals('value2', $options['label2']->getValue());
    }

    public function testGetAttributeMetadataWithoutOptions()
    {
        $this->_eavConfigMock->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnValue($this->_attributeEntityMock));

        $this->_attributeEntityMock->expects($this->any())
            ->method('getSource')
            ->will($this->returnValue($this->_sourceMock));

        $this->_sourceMock->expects($this->any())
            ->method('getAllOptions')
            ->will($this->returnValue(array()));

        $attributeColMock = $this->getMockBuilder('\\Magento\\Customer\\Model\\Resource\\Form\\Attribute\\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $storeManagerMock = $this->getMockBuilder('\\Magento\\Core\\Model\\StoreManager')
            ->disableOriginalConstructor()
            ->getMock();

        $attributeMetadataBuilder = new \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadataBuilder();

        $optionBuilder = new \Magento\Customer\Service\V1\Dto\Eav\OptionBuilder();

        $service = new CustomerMetadataService($this->_eavConfigMock, $attributeColMock, $storeManagerMock,
            $optionBuilder, $attributeMetadataBuilder);

        $attributeMetadata = $service->getAttributeMetadata('entityCode', 'attributeId');
        $this->assertMetadataAttributes($attributeMetadata);

        $options = $attributeMetadata->getOptions();
        $this->assertEquals(0, count($options));
    }

    public function testGetAttributeMetadataWithoutSource()
    {
        $this->_eavConfigMock->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnValue($this->_attributeEntityMock));

        $this->_attributeEntityMock->expects($this->any())
            ->method('getSource')
            ->will($this->throwException(new \Exception('exception message')));

        $attributeColMock = $this->getMockBuilder('\\Magento\\Customer\\Model\\Resource\\Form\\Attribute\\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $storeManagerMock = $this->getMockBuilder('\\Magento\\Core\\Model\\StoreManager')
            ->disableOriginalConstructor()
            ->getMock();

        $optionBuilder = new \Magento\Customer\Service\V1\Dto\Eav\OptionBuilder();

        $attributeMetadataBuilder = new \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadataBuilder();

        $service = new CustomerMetadataService($this->_eavConfigMock, $attributeColMock, $storeManagerMock,
            $optionBuilder, $attributeMetadataBuilder);

        $attributeMetadata = $service->getAttributeMetadata('entityCode', 'attributeId');
        $this->assertMetadataAttributes($attributeMetadata);

        $options = $attributeMetadata->getOptions();
        $this->assertEquals(0, count($options));
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param array $valueMap
     */
    private function _mockReturnValue($mock, $valueMap)
    {
        foreach ($valueMap as $method => $value) {
            $mock->expects($this->any())
                ->method($method)
                ->will($this->returnValue($value));
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
        $this->assertEquals(self::VALIDATE_RULES, $attributeMetadata->getValidationRules());
        $this->assertEquals(self::FRONTEND_CLASS, $attributeMetadata->getFrontendClass());
    }
}
