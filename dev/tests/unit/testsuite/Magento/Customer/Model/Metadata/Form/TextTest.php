<?php
/**
 * test Magento\Customer\Model\Metadata\Form\Text
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata\Form;

use Magento\Customer\Service\V1\Dto\Eav\ValidationRule;

class TextTest extends AbstractFormTestCase
{
    /** @var \Magento\Stdlib\String */
    protected $stringHelper;

    protected function setUp()
    {
        parent::setUp();
        $this->stringHelper = new \Magento\Stdlib\String();
    }

    /**
     * Create an instance of the class that is being tested
     *
     * @param string|int|bool|null $value The value undergoing testing by a given test
     * @return Text
     */
    protected function getClass($value)
    {
        return new Text(
            $this->localeMock,
            $this->loggerMock,
            $this->attributeMetadataMock,
            $this->localeResolverMock,
            $value,
            0,
            false,
            $this->stringHelper
        );
    }

    /**
     * @param string|int|bool $value to assign to boolean
     * @param bool $expected text output
     * @dataProvider validateValueDataProvider
     */
    public function testValidateValue($value, $expected)
    {
        $sut = $this->getClass($value);
        $actual = $sut->validateValue($value);
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
     * @param string|bool|null $expected text output
     * @dataProvider validateValueRequiredDataProvider
     */
    public function testValidateValueRequired($value, $expected)
    {
        $this->attributeMetadataMock
            ->expects($this->any())
            ->method('isRequired')
            ->will($this->returnValue(true));

        $sut = $this->getClass($value);
        $actual = $sut->validateValue($value);

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
            'zero'  => ['0', true],
            'string' => ['some text', true],
            'number' => [123, true],
            'true' => [true, true],
            'false' => [false, '"" is a required value.'],
        ];
    }

    /**
     * @param string|int|bool|null $value to assign to boolean
     * @param string|bool $expected text output
     * @dataProvider validateValueLengthDataProvider
     */
    public function testValidateValueLength($value, $expected)
    {
        $validationRules = [
            'min_text_length' => new ValidationRule(['name' => 'min_text_length', 'value' => 4]),
            'max_text_length' => new ValidationRule(['name' => 'max_text_length', 'value' => 8])
        ];

        $this->attributeMetadataMock
            ->expects($this->any())
            ->method('getValidationRules')
            ->will($this->returnValue($validationRules));

        $sut = $this->getClass($value);
        $actual = $sut->validateValue($value);

        if (is_bool($actual)) {
            $this->assertEquals($expected, $actual);
        } else {
            $this->assertContains($expected, $actual);
        }
    }

    public function validateValueLengthDataProvider()
    {
        return [
            'false' => [false, true],
            'empty' => ['', true],
            'null' => [null, true],
            'true' => [true, '"" length must be equal or greater than 4 characters.'],
            'one' => [1, '"" length must be equal or greater than 4 characters.'],
            'L1' => ['a', '"" length must be equal or greater than 4 characters.'],
            'L3' => ['abc', '"" length must be equal or greater than 4 characters.'],
            'L4' => ['abcd', true],
            'thousand' => [1000, true],
            'L8' => ['abcdefgh', true],
            'L9' => ['abcdefghi', '"" length must be equal or less than 8 characters.'],
            'L12' => ['abcdefghjkl', '"" length must be equal or less than 8 characters.'],
            'billion' => [1000000000, '"" length must be equal or less than 8 characters.'],
        ];
    }
}
