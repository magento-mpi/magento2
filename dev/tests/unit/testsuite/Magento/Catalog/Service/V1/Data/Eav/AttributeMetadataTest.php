<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

class AttributeMetadataTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\Service\Data\AbstractObjectBuilder|\PHPUnit_Framework_TestCase */
    protected $builderMock;

    /** @var \Magento\Catalog\Service\V1\Data\Eav\ValidationRule[] */
    protected $validationRules;

    /** @var \Magento\Catalog\Service\V1\Data\Eav\Option[] */
    protected $optionRules;

    protected function setUp()
    {
        $this->builderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(array('getData'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->validationRules = array(
            $this->getMock('Magento\Catalog\Service\V1\Data\Eav\ValidationRule', [], [], '', false),
            $this->getMock('Magento\Catalog\Service\V1\Data\Eav\ValidationRule', [], [], '', false)
        );

        $this->optionRules = array(
            $this->getMock('Magento\Catalog\Service\V1\Data\Eav\Option', [], [], '', false),
            $this->getMock('Magento\Catalog\Service\V1\Data\Eav\Option', [], [], '', false)
        );
    }

    /**
     * Test constructor and getters
     *
     * @dataProvider constructorAndGettersDataProvider
     */
    public function testConstructorAndGetters($method, $key, $expectedValue)
    {
        $this->builderMock
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue([$key => $expectedValue]));
        $attributeMetadata = new AttributeMetadata($this->builderMock);
        $this->assertEquals($expectedValue, $attributeMetadata->$method());
    }

    public function constructorAndGettersDataProvider()
    {
        return array(
            ['getAttributeCode', AttributeMetadata::ATTRIBUTE_CODE, 'code'],
            ['getFrontendInput', AttributeMetadata::FRONTEND_INPUT, '<br>'],
            ['getInputFilter', AttributeMetadata::INPUT_FILTER, 'date'],
            ['getStoreLabel', AttributeMetadata::STORE_LABEL, 'My Store'],
            ['getValidationRules', AttributeMetadata::VALIDATION_RULES, $this->validationRules],
            ['getMultilineCount', AttributeMetadata::MULTILINE_COUNT, 100],
            ['isVisible', AttributeMetadata::VISIBLE, true],
            ['isRequired', AttributeMetadata::REQUIRED, true],
            ['getDataModel', AttributeMetadata::DATA_MODEL, 'Model'],
            ['getOptions', AttributeMetadata::OPTIONS, $this->optionRules],
            ['getFrontendClass', AttributeMetadata::FRONTEND_CLASS, 'Class'],
            ['isUserDefined', AttributeMetadata::IS_USER_DEFINED, false],
            ['getSortOrder', AttributeMetadata::SORT_ORDER, 100],
            ['getFrontendLabel', AttributeMetadata::FRONTEND_LABEL, 'Label'],
            ['getNote', AttributeMetadata::NOTE, 'Text Note'],
            ['isSystem', AttributeMetadata::IS_SYSTEM, false],
            ['getBackendType', AttributeMetadata::BACKEND_TYPE, 'Type']
        );
    }
}
