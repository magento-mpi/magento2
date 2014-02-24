<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata\Form;
use Magento\Customer\Service\V1\Data\Eav\Option;
use Magento\Customer\Service\V1\Data\Eav\OptionBuilder;

/**
 * test Magento\Customer\Model\Metadata\Form\Select
 */
class SelectTest extends AbstractFormTestCase
{
    /**
     * Create an instance of the class that is being tested
     *
     * @param string|int|bool|null $value The value undergoing testing by a given test
     * @return Select
     */
    protected function getClass($value)
    {
        return new Select(
            $this->localeMock,
            $this->loggerMock,
            $this->attributeMetadataMock,
            $value,
            0
        );
    }

    /**
     * @param string|int|bool|null $value to assign to Select
     * @param bool $expected text output
     * @dataProvider validateValueDataProvider
     */
    public function testValidateValue($value, $expected)
    {
        $select = $this->getClass($value);
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

        $select = $this->getClass($value);
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
                (new OptionBuilder())->setValue('14')->setLabel('fourteen')->create(),
                (new OptionBuilder())->setValue('some key')->setLabel('some string')->create(),
                (new OptionBuilder())->setValue('true')->setLabel('True')->create(),
            ]));
        $select = $this->getClass($value);
        $actual = $select->outputValue();
        $this->assertEquals($expected, $actual);
    }

    public function outputValueDataProvider()
    {
        return [
            'empty' => ['', ''],
            'null' => [null, ''],
            'number' => [14, 'fourteen'],
            'string' => ['some key', 'some string'],
            'boolean' => [true, ''],
            'unknown' => ['unknownKey', ''],
            'true' => ['true', 'True'],
        ];
    }
}
