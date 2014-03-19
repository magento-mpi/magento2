<?php
/**
 * test Magento\Customer\Model\Metadata\Form\Boolean
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata\Form;

class BooleanTest extends AbstractFormTestCase
{
    /**
     * @param mixed $value to assign to boolean
     * @param mixed $expected text output
     * @dataProvider getOptionTextDataProvider
     */
    public function testGetOptionText($value, $expected)
    {
        // calling outputValue() will cause the protected method getOptionText() to be called
        $boolean = new Boolean(
            $this->localeMock,
            $this->loggerMock,
            $this->attributeMetadataMock,
            $this->localeResolverMock,
            $value,
            0
        );
        $this->assertSame($expected, $boolean->outputValue());
    }

    public function getOptionTextDataProvider()
    {
        return array(
            '0' => array('0', 'No'),
            '1' => array('1', 'Yes'),
            'int 5' => array(5, ''),
            'Null' => array(null, ''),
            'Invalid' => array('Invalid', ''),
            'Empty string' => array('', '')
        );
    }
}
