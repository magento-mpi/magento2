<?php
/**
 * test Magento\Customer\Model\Model\Metadata\Form\Date
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata\Form;

class DateTest extends \PHPUnit_Framework_TestCase
{

    /** @var \Magento\Core\Model\LocaleInterface | \PHPUnit_Framework_MockObject_MockObject */
    protected $localeMock;

    /** @var \Magento\Logger | \PHPUnit_Framework_MockObject_MockObject */
    protected $loggerMock;

    /** @var \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata | \PHPUnit_Framework_MockObject_MockObject */
    protected $attributeMetadataMock;

    /** @var \Magento\Customer\Model\Metadata\Form\Date */
    protected $date;

    protected function setUp()
    {
        $this->localeMock = $this->getMockBuilder('Magento\Core\Model\LocaleInterface')->getMock();
        $this->loggerMock = $this->getMockBuilder('Magento\Logger')->disableOriginalConstructor()->getMock();
        $this->attributeMetadataMock = $this->getMockBuilder('Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeMetadataMock->expects($this->any())
            ->method('getAttributeCode')
            ->will($this->returnValue('date'));
        $this->attributeMetadataMock->expects($this->any())
            ->method('getStoreLabel')
            ->will($this->returnValue('Space Date'));
        $this->attributeMetadataMock->expects($this->any())
            ->method('getInputFilter')
            ->will($this->returnValue('date'));
        $this->date = new Date($this->localeMock, $this->loggerMock, $this->attributeMetadataMock, null, 0);
    }

    public function testExtractValue()
    {
        $requestMock = $this->getMockBuilder('Magento\App\RequestInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock->expects($this->once())->method('getParam')->will($this->returnValue('1999-1-2'));

        // yyyy-MM-dd
        $actual = $this->date->extractValue($requestMock);
        $this->assertEquals('1999-01-02', $actual);
    }

    /**
     * @param $value Value to validate
     * @param $validation Array of more validation metadata
     * @param $required Whether field is required
     * @param $expected Expected output
     *
     * @dataProvider validateValueDataProvider
     */
    public function testValidateValue($value, $validation, $required, $expected)
    {
        $this->attributeMetadataMock->expects($this->any())
            ->method('getValidationRules')
            ->will($this->returnValue(array_merge(['input_validation' => 'date'], $validation)));

        $this->attributeMetadataMock->expects($this->any())
            ->method('isRequired')
            ->will($this->returnValue($required));

        $actual = $this->date->validateValue($value);
        $this->assertEquals($expected, $actual);
    }

    public function validateValueDataProvider()
    {
        return [
            'false value, load original' => [false, [], false, true],
            'Empty value, not required' => ['', [], false, true],
            'Empty value, required' => ['', [], true, ['"Space Date" is a required value.']],
            'Valid date, min set' => ['1961-5-5', ['date_range_min' => strtotime('4/12/1961')], false, true],
            'Below min, only min set' => [
                '1957-10-4',
                ['date_range_min' => strtotime('1961/04/12')],
                false,
                ['Please enter a valid date equal to or greater than 12/04/1961 at Space Date.'],
            ],
            'Below min, min and max set' => [
                '1957-10-4',
                ['date_range_min' => strtotime('1961/04/12'), 'date_range_max' => strtotime('12/1/2013')],
                false,
                ['Please enter a valid date between 12/04/1961 and 01/12/2013 at Space Date.'],
            ],
            'Above max, only max set' => [
                '2014-1-30',
                ['date_range_max' => strtotime('12/1/2013')],
                false,
                ['Please enter a valid date less than or equal to 01/12/2013 at Space Date.'],
            ],
            'Valid, min and max' => [
                '1961-5-5',
                [ 'date_range_min' => strtotime('4/12/1961'), 'date_range_max' => strtotime('12/1/2013')],
                false,
                true,
            ],
            'Invalid date' => [
                'abc',
                [],
                false,
                ['dateFalseFormat' => '"Space Date" does not fit the entered date format.']
            ],
        ];
    }

    /**
     * @param $value value to pass to compactValue()
     * @param $expected expected output
     *
     * @dataProvider compactAndRestoreValueDataProvider
     */
    public function testCompactValue($value, $expected)
    {
        $this->assertSame($expected, $this->date->compactValue($value));
    }

    public function compactAndRestoreValueDataProvider()
    {
        return [
            [1, 1],
            [false, false],
            ['', null],
        ];
    }

    /**
     * @param $value Value to pass to restoreValue()
     * @param $expected Expected output
     *
     * @dataProvider compactAndRestoreValueDataProvider
     */
    public function testRestoreValue($value, $expected)
    {
        $this->assertSame($expected, $this->date->restoreValue($value));
    }

    public function testOutputValue()
    {
        $this->assertEquals(null, $this->date->outputValue());
        $date = new Date($this->localeMock, $this->loggerMock, $this->attributeMetadataMock, '2012/12/31', 0);
        $this->assertEquals('2012-12-31', $date->outputValue());

    }
}
