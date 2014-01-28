<?php
/**
 * test Magento\Customer\Model\Model\Metadata\Form\Text
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata\Form;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Core\Model\LocaleInterface */
    protected $localeMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Logger */
    protected $loggerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Stdlib\String */
    protected $attributeMetadataMock;

    protected function setUp()
    {
        $this->localeMock = $this->getMockBuilder('Magento\Core\Model\LocaleInterface')->getMock();
        $this->loggerMock = $this->getMockBuilder('Magento\Logger')->disableOriginalConstructor()->getMock();
        $this->attributeMetadataMock = $this->getMockBuilder('Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param mixed $value to assign to boolean
     * @param mixed $expected text output
     * @dataProvider validateValueDataProvider
     */
    public function testValidateValue($value, $expected)
    {
        $select = new Select($this->localeMock, $this->loggerMock, $this->attributeMetadataMock, $value, 0);
        $actual = $select->validateValue($value);
        $this->assertEquals($expected, $actual);
    }

    public function validateValueDataProvider()
    {
        return [
            'empty' => ['', true],
            '0' => [0, true],
            'zero' => ['0', true],
            'string' => ['some text', true],
            'number' => [123, true],
            'true' => [true, true],
            'false' => [false, true]
        ];
    }

    /**
     * @param mixed $value to assign to boolean
     * @param mixed $expected text output
     * @dataProvider validateValueRequiredDataProvider
     */
    public function testValidateValueRequired($value, $expected)
    {
        $this->attributeMetadataMock
            ->expects($this->any())
            ->method('isRequired')
            ->will($this->returnValue(true));

        $select = new Select($this->localeMock, $this->loggerMock, $this->attributeMetadataMock, $value, 0);
        $actual = $select->validateValue($value);

        if (is_bool($actual)) {
            $this->assertEquals($expected, $actual);
        }
        else {
            $this->assertContains($expected, $actual);
        }
    }

    public function validateValueRequiredDataProvider()
    {
        return [
            'empty' => ['', '"" is a required value.'],
            'null' => [null, '"" is a required value.'],
            '0'  => [0, true],
            'string' => ['some text', true],
            'number' => [123, true],
            'true' => [true, true],
            'false' => [false, '"" is a required value.'],
        ];
    }

    /**
     * @param mixed $value 
     * @dataProvider outputValueJsonDataProvider
     */
    public function testOutputValueJson($value)
    {
        $select = new Select($this->localeMock, $this->loggerMock, $this->attributeMetadataMock, $value, 0);
        $actual = $select->outputValue(\Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_JSON);
        $this->assertEquals($value, $actual);
    }
    
    public function outputValueJsonDataProvider()
    {
        return [
            'empty' => [''],
            'null' => [null],
            'number' => [15],
            'string' => ['some string'],
            'boolean' => [true],            
        ];
    }
    
    /**
     * @param mixed $value
     * @param mixed $expected
     * @dataProvider outputValueTextDataProvider
     */
    public function testOutputValueText($value, $expected)
    {
        $this->attributeMetadataMock
            ->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue([
                new \Magento\Customer\Service\V1\Dto\Eav\Option(['value' => 14, 'label' => 'fourteen']),
                new \Magento\Customer\Service\V1\Dto\Eav\Option(['value' => 'some key', 'label' => 'some string']),
            ]));
        $select = new Select($this->localeMock, $this->loggerMock, $this->attributeMetadataMock, $value, 0);
        $actual = $select->outputValue(\Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_TEXT);
        $this->assertEquals($expected, $actual);
    }
    
    public function outputValueTextDataProvider()
    {
        return [
            'empty' => ['', ''],
            'null' => [null, ''],
            'number' => ['fourteen', 14],
            'string' => ['some string', 'some key'],
            'boolean' => [true, '14'],            
        ];
    }    
}