<?php
/**
 * test Magento\Customer\Model\Metadata\Form\Multiselect
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata\Form;

use Magento\Customer\Model\Metadata\ElementFactory;
use Magento\Customer\Api\Data\OptionDataBuilder;

class MultiselectTest extends AbstractFormTestCase
{
    /**
     * Create an instance of the class that is being tested
     *
     * @param string|int|bool|null $value The value undergoing testing by a given test
     *
     * @return Multiselect
     */
    protected function getClass($value)
    {
        return new Multiselect(
            $this->localeMock,
            $this->loggerMock,
            $this->attributeMetadataMock,
            $this->localeResolverMock,
            $value,
            0
        );
    }

    /**
     * Test the Multiselect->extractValue() method
     *
     * @param string|int|bool|array $value to assign to boolean
     * @param bool $expected text output
     *
     * @return void
     * @dataProvider extractValueDataProvider
     */
    public function testExtractValue($value, $expected)
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject | Multiselect $multiselect */
        $multiselect = $this->getMockBuilder(
            'Magento\Customer\Model\Metadata\Form\Multiselect'
        )->disableOriginalConstructor()->setMethods(
            array('_getRequestValue')
        )->getMock();
        $multiselect->expects($this->once())->method('_getRequestValue')->will($this->returnValue($value));

        $request = $this->getMockBuilder('Magento\Framework\App\RequestInterface')->getMock();
        $actual = $multiselect->extractValue($request);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for testExtractValue()
     *
     * @return array(array)
     */
    public function extractValueDataProvider()
    {
        return array(
            'false' => array(false, false),
            'int' => array(15, array(15)),
            'string' => array('some string', array('some string')),
            'array' => array(array(1, 2, 3), array(1, 2, 3))
        );
    }

    /**
     * Test the Multiselect->compactValue() method
     *
     * @param string|int|bool|array $value to assign to boolean
     * @param bool $expected text output
     *
     * @return void
     * @dataProvider compactValueDataProvider
     */
    public function testCompactValue($value, $expected)
    {
        $multiselect = $this->getClass($value);
        $actual = $multiselect->compactValue($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for testCompactValue()
     *
     * @return array(array)
     */
    public function compactValueDataProvider()
    {
        return array(
            'false' => array(false, false),
            'int' => array(15, 15),
            'string' => array('some string', 'some string'),
            'array' => array(array(1, 2, 3), '1,2,3')
        );
    }

    /**
     * Test the Multiselect->outputValue() method with default TEXT format
     *
     * @param string|int|null|string[]|int[] $value
     * @param string $expected
     *
     * @return void
     * @dataProvider outputValueTextDataProvider
     */
    public function testOutputValueText($value, $expected)
    {
        $this->runOutputValueTest($value, $expected, ElementFactory::OUTPUT_FORMAT_TEXT);
    }

    /**
     * Test the Multiselect->outputValue() method with default HTML format
     *
     * @param string|int|null|string[]|int[] $value
     * @param string $expected
     *
     * @return void
     * @dataProvider outputValueTextDataProvider
     */
    public function testOutputValueHtml($value, $expected)
    {
        $this->runOutputValueTest($value, $expected, ElementFactory::OUTPUT_FORMAT_HTML);
    }

    /**
     * Data provider for testOutputValueText()
     *
     * @return array(array)
     */
    public function outputValueTextDataProvider()
    {
        return array(
            'empty' => array('', ''),
            'null' => array(null, ''),
            'number' => array(14, 'fourteen'),
            'string' => array('some key', 'some string'),
            'array' => array(array(14, 'some key'), 'fourteen, some string'),
            'unknown' => array(array(14, 'some key', 'unknown'), 'fourteen, some string, ')
        );
    }

    /**
     * Test the Multiselect->outputValue() method with JSON format
     *
     * @param string|int|null|string[]|int[] $value
     * @param string[] $expected
     *
     * @return void
     * @dataProvider outputValueJsonDataProvider
     */
    public function testOutputValueJson($value, $expected)
    {
        $this->runOutputValueTest($value, $expected, ElementFactory::OUTPUT_FORMAT_JSON);
    }

    /**
     * Data provider for testOutputValueJson()
     *
     * @return array(array)
     */
    public function outputValueJsonDataProvider()
    {
        return array(
            'empty' => array('', array('')),
            'null' => array(null, array('')),
            'number' => array(14, array('14')),
            'string' => array('some key', array('some key')),
            'array' => array(array(14, 'some key'), array('14', 'some key')),
            'unknown' => array(array(14, 'some key', 'unknown'), array('14', 'some key', 'unknown'))
        );
    }

    /**
     * Helper function that runs an outputValue test for a given format.
     *
     * @param string|int|null|string[]|int[] $value
     * @param string|string[] $expected
     * @param string $format
     */
    protected function runOutputValueTest($value, $expected, $format)
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
                        ->setValue('some key')->setLabel('some string')->create()
                )
            )
        );
        $multiselect = $this->getClass($value);
        $actual = $multiselect->outputValue($format);
        $this->assertEquals($expected, $actual);
    }
}
