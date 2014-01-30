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

class MultiselectTest extends AbstractFormTestCase
{
    /**
     * @param string|int|bool|array $value to assign to boolean
     * @param bool $expected text output
     * @dataProvider extractValueDataProvider
     */
    public function testExtractValue($value, $expected)
    {
        $multiselect = $this->getMockBuilder('Magento\Customer\Model\Metadata\Form\Multiselect')
            ->disableOriginalConstructor()
            ->setMethods(['_getRequestValue'])
            ->getMock();
        $multiselect->expects($this->once())
            ->method('_getRequestValue')
            ->will($this->returnValue($value));

        // $multiselect = new Multiselect($this->localeMock, $this->loggerMock, $this->attributeMetadataMock, $value, 0);

        $request = $this->getMockBuilder('Magento\App\RequestInterface')->getMock();
        $actual = $multiselect->extractValue($request);
        $this->assertEquals($expected, $actual);
    }

    public function extractValueDataProvider()
    {
        return [
            'false' => [false, false],
            'int' => [15, [15]],
            'string' => ['some string', ['some string']],
            'array' => [[1, 2, 3], [1, 2, 3]]
        ];
    }

    /**
     * @param string|int|bool|array $value to assign to boolean
     * @param bool $expected text output
     * @dataProvider compactValueDataProvider
     */
    public function testCompactValue($value, $expected)
    {
        $multiselect = new Multiselect($this->localeMock, $this->loggerMock, $this->attributeMetadataMock, $value, 0);
        $actual = $multiselect->compactValue($value);
        $this->assertEquals($expected, $actual);
    }

    public function compactValueDataProvider()
    {
        return [
            'false' => [false, false],
            'int' => [15, 15],
            'string' => ['some string', 'some string'],
            'array' => [[1, 2, 3], '1,2,3']
        ];
    }

    /**
     * @param string|int|null $value
     * @param string|int $expected
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
        $multiselect = new Multiselect($this->localeMock, $this->loggerMock, $this->attributeMetadataMock, $value, 0);
        $actual = $multiselect->outputValue();
        $this->assertEquals($expected, $actual);
    }

    public function outputValueTextDataProvider()
    {
        return [
            'empty' => ['', ''],
            'null' => [null, ''],
            'number' => ['fourteen', 14],
            'string' => ['some string', 'some key'],
            'array' => [['fourteen', 'some string'], '14, some key']
        ];
    }

    /**
     * @param string|int|null $value
     * @param string|int $expected
     * @dataProvider outputValueJsonDataProvider
     */
    public function testOutputValueJson($value, $expected)
    {
        $this->attributeMetadataMock
            ->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue([
                new \Magento\Customer\Service\V1\Dto\Eav\Option(['value' => 14, 'label' => 'fourteen']),
                new \Magento\Customer\Service\V1\Dto\Eav\Option(['value' => 'some key', 'label' => 'some string']),
            ]));
        $multiselect = new Multiselect($this->localeMock, $this->loggerMock, $this->attributeMetadataMock, $value, 0);
        $actual = $multiselect->outputValue(\Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_JSON);
        $this->assertEquals($expected, $actual);
    }

    public function outputValueJsonDataProvider()
    {
        return [
            'empty' => ['', []],
            'null' => [null, []],
            'number' => ['fourteen', [14]],
            'string' => ['some string', ['some key']],
            'array' => [['fourteen', 'some string'], [14, 'some key']]
        ];
    }
}
