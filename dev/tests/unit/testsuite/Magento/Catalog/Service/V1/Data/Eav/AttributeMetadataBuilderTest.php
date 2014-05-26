<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;

class AttributeMetadataBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder|\PHPUnit_Framework_TestCase */
    protected $attributeMetadataBuilder;

    /** @var \Magento\Catalog\Service\V1\Data\Eav\OptionBuilder */
    private $optionBuilderMock;

    /** @var \Magento\Catalog\Service\V1\Data\Eav\ValidationRuleBuilder */
    private $validationRuleBuilderMock;

    /** @var \Magento\Catalog\Service\V1\Data\Eav\ValidationRule[] */
    protected $validationRules;

    /** @var \Magento\Catalog\Service\V1\Data\Eav\Option[] */
    protected $optionRules;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        
        $this->optionBuilderMock =
            $this->getMock('Magento\Catalog\Service\V1\Data\Eav\OptionBuilder', [], [], '', false);

        $this->validationRuleBuilderMock =
            $this->getMock('Magento\Catalog\Service\V1\Data\Eav\ValidationRuleBuilder', [], [], '', false);

        $this->validationRules = array(
            [0 => $this->getMock('Magento\Catalog\Service\V1\Data\Eav\ValidationRule', [], [], '', false)],
            [1 => $this->getMock('Magento\Catalog\Service\V1\Data\Eav\ValidationRule', [], [], '', false)]
        );

        $this->optionRules = array(
            [0 => $this->getMock('Magento\Catalog\Service\V1\Data\Eav\Option', [], [], '', false)],
            [1 => $this->getMock('Magento\Catalog\Service\V1\Data\Eav\Option', [], [], '', false)]
        );

        $this->attributeMetadataBuilder = $objectManager->getObject(
            'Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder',
            [
                'optionBuilder' => $this->optionBuilderMock,
                'validationRuleBuilder' => $this->validationRuleBuilderMock
            ]
        );
    }

    /**
     * @dataProvider setValueDataProvider
     */
    public function testSetValue($method, $value, $getMethod)
    {
        $data = $this->attributeMetadataBuilder->$method($value)->create();
        $this->assertEquals($value, $data->$getMethod());
    }

    public function setValueDataProvider()
    {
        return array(
            ['setAttributeCode', 'code', 'getAttributeCode'],
            ['setFrontendInput', '<br>', 'getFrontendInput'],
            ['setInputFilter', 'date', 'getInputFilter'],
            ['setStoreLabel', 'My Store', 'getStoreLabel'],
            ['setValidationRules', $this->validationRules, 'getValidationRules'],
            ['setMultilineCount', 100, 'getMultilineCount'],
            ['setVisible', true, 'isVisible'],
            ['setRequired', true, 'isRequired'],
            ['setDataModel', 'Model', 'getDataModel'],
            ['setOptions', $this->optionRules, 'getOptions'],
            ['setFrontendClass', 'Class', 'getFrontendClass'],
            ['setIsUserDefined', false, 'isUserDefined'],
            ['setSortOrder', 100, 'getSortOrder'],
            ['setFrontendLabel', 'Label', 'getFrontendLabel'],
            ['setNote', 'Text Note', 'getNote'],
            ['setIsSystem', false, 'isSystem'],
            ['setBackendType', 'Type', 'getBackendType']
        );
    }

    public function testPopulateWithArray()
    {
        $this->optionBuilderMock
            ->expects($this->at(0))
            ->method('populateWithArray')
            ->with($this->optionRules[0])
            ->will($this->returnSelf());
        $this->optionBuilderMock
            ->expects($this->at(1))
            ->method('create')
            ->will($this->returnValue($this->optionRules[0]));
        $this->optionBuilderMock
            ->expects($this->at(2))
            ->method('populateWithArray')
            ->with($this->optionRules[1])
            ->will($this->returnSelf());
        $this->optionBuilderMock
            ->expects($this->at(3))
            ->method('create')
            ->will($this->returnValue($this->optionRules[1]));

        $this->validationRuleBuilderMock
            ->expects($this->at(0))
            ->method('populateWithArray')
            ->with($this->validationRules[0])
            ->will($this->returnSelf());
        $this->validationRuleBuilderMock
            ->expects($this->at(1))
            ->method('create')
            ->will($this->returnValue($this->validationRules[0]));
        $this->validationRuleBuilderMock
            ->expects($this->at(2))
            ->method('populateWithArray')
            ->with($this->validationRules[1])
            ->will($this->returnSelf());
        $this->validationRuleBuilderMock
            ->expects($this->at(3))
            ->method('create')
            ->will($this->returnValue($this->validationRules[1]));

        $data = array(
            AttributeMetadata::OPTIONS => $this->optionRules,
            AttributeMetadata::VALIDATION_RULES => $this->validationRules,
            'note' => $textNote = 'Text Note',
            'visible' => $visible = true,
            'some_key' => 'some_value',
        );

        $attributeData = $this->attributeMetadataBuilder->populateWithArray($data)->create();
        $this->assertEquals($textNote, $attributeData->getNote());
        $this->assertEquals($visible, $attributeData->isVisible());
        $this->assertEquals($data[AttributeMetadata::OPTIONS], $attributeData->getOptions());
        $this->assertEquals($data[AttributeMetadata::VALIDATION_RULES], $attributeData->getValidationRules());
        $this->assertArrayNotHasKey('some_key',$attributeData->__toArray());
    }
}
