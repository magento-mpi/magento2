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

class TextTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Core\Model\LocaleInterface */
    protected $_localeMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Logger */
    protected $_loggerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata */
    protected $_attributeMetadataMock;

    /** @var \Magento\Stdlib\String */
    protected $_stringHelper;

    protected function setUp()
    {
        $this->_localeMock = $this->getMockBuilder('Magento\Core\Model\LocaleInterface')->getMock();
        $this->_loggerMock = $this->getMockBuilder('Magento\Logger')->disableOriginalConstructor()->getMock();
        $this->_stringHelper = new \Magento\Stdlib\String();
        $this->_attributeMetadataMock =
            $this->getMockBuilder('Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata')
                ->disableOriginalConstructor()
                ->getMock();
    }

    /**
     * Create an instance of the class that is being tested
     *
     * @param string|int|bool|null $value The value undergoing testing by a given test
     * @return Text
     */
    protected function _getClass($value)
    {
        return new Text(
            $this->_localeMock,
            $this->_loggerMock,
            $this->_attributeMetadataMock,
            $value,
            0,
            false,
            $this->_stringHelper
        );
    }

    /**
     * @param string|int|bool $value to assign to boolean
     * @param bool $expected text output
     * @dataProvider validateValueDataProvider
     */
    public function testValidateValue($value, $expected)
    {
        $sut = $this->_getClass($value);
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
        $this->_attributeMetadataMock
            ->expects($this->any())
            ->method('isRequired')
            ->will($this->returnValue(true));

        $sut = $this->_getClass($value);
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
        $this->_attributeMetadataMock
            ->expects($this->any())
            ->method('getValidationRules')
            ->will($this->returnValue(['min_text_length' => 4, 'max_text_length' => 8]));

        $sut = $this->_getClass($value);
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
