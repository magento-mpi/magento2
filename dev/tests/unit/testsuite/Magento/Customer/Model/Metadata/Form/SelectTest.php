<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata\Form;

use Magento\Customer\Model\Data\Option;
use Magento\Customer\Api\Data\OptionDataBuilder;

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
            $this->localeResolverMock,
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
     * @param string|bool $expected text output
     * @dataProvider validateValueRequiredDataProvider
     */
    public function testValidateValueRequired($value, $expected)
    {
        $this->attributeMetadataMock->expects($this->any())->method('isRequired')->will($this->returnValue(true));

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
        return array(
            'empty' => array('', '"" is a required value.'),
            'null' => array(null, '"" is a required value.'),
            '0' => array(0, true),
            'string' => array('some text', true),
            'number' => array(123, true),
            'true' => array(true, true),
            'false' => array(false, '"" is a required value.')
        );
    }

    /**
     * @param string|int|bool|null $value
     * @param string|int $expected
     * @dataProvider outputValueDataProvider
     */
    public function testOutputValue($value, $expected)
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->attributeMetadataMock->expects(
            $this->any()
        )->method(
            'getOptions'
        )->will(
            $this->returnValue(
                array(
                    $helper->getObject('\Magento\Customer\Api\Data\OptionDataBuilder')
                        ->setValue('14')->setLabel('fourteen')->create(),
                    $helper->getObject('\Magento\Customer\Api\Data\OptionDataBuilder')
                        ->setValue('some key')->setLabel('some string')->create(),
                    $helper->getObject('\Magento\Customer\Api\Data\OptionDataBuilder')
                        ->setValue('true')->setLabel('True')->create()
                )
            )
        );
        $select = $this->getClass($value);
        $actual = $select->outputValue();
        $this->assertEquals($expected, $actual);
    }

    public function outputValueDataProvider()
    {
        return array(
            'empty' => array('', ''),
            'null' => array(null, ''),
            'number' => array(14, 'fourteen'),
            'string' => array('some key', 'some string'),
            'boolean' => array(true, ''),
            'unknown' => array('unknownKey', ''),
            'true' => array('true', 'True')
        );
    }
}
