<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata\Form;

/**
 * test Magento\Customer\Model\Metadata\Form\Select
 */
class SelectTest extends AbstractFormTestCase
{

    /**
     * @param string|int|bool|null $value to assign to boolean
     * @param bool $expected text output
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
     * @param string|int|bool|null $value to assign to boolean
     * @param string|bool $expected text output
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
        } else {
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
     * @param string|int|bool|null $value
     * @param string|int $expected
     * @dataProvider outputValueDataProvider
     */
    public function testOutputValue($value, $expected)
    {
        $this->attributeMetadataMock
            ->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue([
                new \Magento\Customer\Service\V1\Dto\Eav\Option(['value' => 14, 'label' => 'fourteen']),
                new \Magento\Customer\Service\V1\Dto\Eav\Option(['value' => 'some key', 'label' => 'some string']),
            ]));
        $select = new Select($this->localeMock, $this->loggerMock, $this->attributeMetadataMock, $value, 0);
        $actual = $select->outputValue();
        $this->assertEquals($expected, $actual);
    }

    public function outputValueDataProvider()
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
