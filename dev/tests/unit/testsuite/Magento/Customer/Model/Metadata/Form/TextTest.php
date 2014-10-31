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

class TextTest extends AbstractFormTestCase
{
    /** @var \Magento\Framework\Stdlib\String */
    protected $stringHelper;

    protected function setUp()
    {
        parent::setUp();
        $this->stringHelper = new \Magento\Framework\Stdlib\String();
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
        return array(
            'empty' => array('', true),
            '0' => array(0, true),
            'zero' => array('0', true),
            'string' => array('some text', true),
            'number' => array(123, true),
            'true' => array(true, true),
            'false' => array(false, true)
        );
    }

    /**
     * @param string|int|bool|null $value to assign to boolean
     * @param string|bool|null $expected text output
     * @dataProvider validateValueRequiredDataProvider
     */
    public function testValidateValueRequired($value, $expected)
    {
        $this->attributeMetadataMock->expects($this->any())->method('isRequired')->will($this->returnValue(true));

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
        return array(
            'empty' => array('', '"" is a required value.'),
            'null' => array(null, '"" is a required value.'),
            '0' => array(0, true),
            'zero' => array('0', true),
            'string' => array('some text', true),
            'number' => array(123, true),
            'true' => array(true, true),
            'false' => array(false, '"" is a required value.')
        );
    }

    /**
     * @param string|int|bool|null $value to assign to boolean
     * @param string|bool $expected text output
     * @dataProvider validateValueLengthDataProvider
     */
    public function testValidateValueLength($value, $expected)
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $minTextLengthRule = $this->getMockBuilder('Magento\Customer\Api\Data\ValidationRuleInterface')
            ->disableOriginalConstructor()
            ->setMethods(['getName', 'getValue'])
            ->getMockForAbstractClass();
        $minTextLengthRule->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('min_text_length'));
        $minTextLengthRule->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue(4));

        $maxTextLengthRule = $this->getMockBuilder('Magento\Customer\Api\Data\ValidationRuleInterface')
            ->disableOriginalConstructor()
            ->setMethods(['getName', 'getValue'])
            ->getMockForAbstractClass();
        $maxTextLengthRule->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('max_text_length'));
        $maxTextLengthRule->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue(8));

        $validationRules = array(
            'min_text_length' => $minTextLengthRule,
            'max_text_length' => $maxTextLengthRule
        );

        $this->attributeMetadataMock->expects(
            $this->any()
        )->method(
            'getValidationRules'
        )->will(
            $this->returnValue($validationRules)
        );

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
        return array(
            'false' => array(false, true),
            'empty' => array('', true),
            'null' => array(null, true),
            'true' => array(true, '"" length must be equal or greater than 4 characters.'),
            'one' => array(1, '"" length must be equal or greater than 4 characters.'),
            'L1' => array('a', '"" length must be equal or greater than 4 characters.'),
            'L3' => array('abc', '"" length must be equal or greater than 4 characters.'),
            'L4' => array('abcd', true),
            'thousand' => array(1000, true),
            'L8' => array('abcdefgh', true),
            'L9' => array('abcdefghi', '"" length must be equal or less than 8 characters.'),
            'L12' => array('abcdefghjkl', '"" length must be equal or less than 8 characters.'),
            'billion' => array(1000000000, '"" length must be equal or less than 8 characters.')
        );
    }
}
