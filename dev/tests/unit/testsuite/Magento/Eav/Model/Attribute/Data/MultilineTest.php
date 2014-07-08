<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Attribute\Data;

class MultilineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Eav\Model\Attribute\Data\Multiline
     */
    protected $model;

    protected function setUp()
    {
        $timezoneMock = $this->getMock('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $loggerMock = $this->getMock('\Magento\Framework\Logger', [], [], '', false);
        $localeResolverMock = $this->getMock('\Magento\Framework\Locale\ResolverInterface');
        $stringMock = $this->getMock('\Magento\Framework\Stdlib\String', [], [], '', false);

        $this->model = new Multiline($timezoneMock, $loggerMock, $localeResolverMock, $stringMock);
    }

    /**
     * @covers \Magento\Eav\Model\Attribute\Data\Multiline::extractValue
     *
     * @param mixed $param
     * @param mixed $expectedResult
     * @dataProvider extractValueDataProvider
     */
    public function testExtractValue($param, $expectedResult)
    {
        $requestMock = $this->getMock('\Magento\Framework\App\RequestInterface');
        $attributeMock = $this->getMock('\Magento\Eav\Model\Attribute', [], [], '', false);

        $requestMock->expects($this->once())->method('getParam')->will($this->returnValue($param));
        $attributeMock->expects($this->once())->method('getAttributeCode')->will($this->returnValue('attributeCode'));

        $this->model->setAttribute($attributeMock);
        $this->assertEquals($expectedResult, $this->model->extractValue($requestMock));
    }

    /**
     * @return array
     */
    public function extractValueDataProvider()
    {
        return [
            [
                'param' => 'param',
                'expectedResult' => false
            ],
            [
                'param' => ['param'],
                'expectedResult' => ['param']
            ],
        ];
    }

    /**
     * @covers \Magento\Eav\Model\Attribute\Data\Multiline::outputValue
     *
     * @param string $format
     * @param mixed $expectedResult
     * @dataProvider outputValueDataProvider
     */
    public function testOutputValue($format, $expectedResult)
    {
        $entityMock = $this->getMock('\Magento\Framework\Model\AbstractModel', [], [], '', false);
        $entityMock->expects($this->once())->method('getData')->will($this->returnValue("value1\nvalue2"));

        $attributeMock = $this->getMock('\Magento\Eav\Model\Attribute', [], [], '', false);

        $this->model->setEntity($entityMock);
        $this->model->setAttribute($attributeMock);
        $this->assertEquals($expectedResult, $this->model->outputValue($format));
    }

    /**
     * @return array
     */
    public function outputValueDataProvider()
    {
        return [
            [
                'format' => \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_ARRAY,
                'expectedResult' => ['value1', 'value2']
            ],
            [
                'format' => \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_HTML,
                'expectedResult' => 'value1<br />value2'
            ],
            [
                'format' => \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_ONELINE,
                'expectedResult' => 'value1 value2'
            ],
            [
                'format' => \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_TEXT,
                'expectedResult' => "value1\nvalue2"
            ]
        ];
    }

    /**
     * @covers \Magento\Eav\Model\Attribute\Data\Multiline::validateValue
     *
     * @param mixed $value
     * @param array $expectedResult
     * @dataProvider validateValueDataProvider
     */
    public function validateValueTest($value, $expectedResult)
    {
        $entityMock = $this->getMock('\Magento\Framework\Model\AbstractModel', [], [], '', false);
        $entityMock->expects($this->once())->method('getData')->will($this->returnValue("value1\nvalue2"));

        $attributeMock = $this->getMock('\Magento\Eav\Model\Attribute', [], [], '', false);

        $this->model->setEntity($entityMock);
        $this->model->setAttribute($attributeMock);
        $this->model->validateValue($value);
    }

    /**
     * @return array
     */
    public function validateValueDataProvider()
    {
        return [];
    }
}
