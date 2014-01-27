<?php
/**
 * test Magento\Customer\Model\Model\Metadata\Form\AbstractData
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata\Form;

class AbstractDataTest extends \PHPUnit_Framework_TestCase
{
    const MODEL = 'MODEL';

    /**
     * @var \Magento\Customer\Model\Metadata\Form\ExtendsAbstractData
     */
    protected $_model;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_localeMock;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_loggerMock;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_attributeMock;

    /** @var string */
    protected $_value;
    /** @var string */
    protected $_entityTypeCode;
    /** @var string */
    protected $_isAjax;

    protected function setUp()
    {
        $this->_localeMock = $this->getMockBuilder('Magento\Core\Model\LocaleInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_loggerMock = $this->getMockBuilder('Magento\Logger')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_attributeMock = $this->getMockBuilder('Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_value = 'VALUE';
        $this->_entityTypeCode = 'I HAVE NO IDEA WHAT THIS SHOULD BE';
        $this->_isAjax = false;

        $this->_model = new ExtendsAbstractData(
            $this->_localeMock,
            $this->_loggerMock,
            $this->_attributeMock,
            $this->_value,
            $this->_entityTypeCode,
            $this->_isAjax
        );
    }

    public function testGetAttribute()
    {
        $this->assertSame($this->_attributeMock, $this->_model->getAttribute());
    }

    /**
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Attribute object is undefined
     */
    public function testGetAttributeException()
    {
        $this->_model->setAttribute(false);
        $this->_model->getAttribute();
    }

    public function testSetRequestScope()
    {
        $this->assertSame($this->_model, $this->_model->setRequestScope('request_scope'));
        $this->assertSame('request_scope', $this->_model->getRequestScope());
    }

    /**
     * @param $bool
     * @dataProvider trueFalseProvider
     */
    public function testSetRequestScopeOnly($bool)
    {
        $this->assertSame($this->_model, $this->_model->setRequestScopeOnly($bool));
        $this->assertSame($bool, $this->_model->isRequestScopeOnly());
    }

    public function trueFalseProvider()
    {
        return [[true], [false]];
    }

    public function testGetSetExtractedData()
    {
        $data = ['key' => 'value'];
        $this->assertSame($this->_model, $this->_model->setExtractedData($data));
        $this->assertSame($data, $this->_model->getExtractedData());
        $this->assertSame('value', $this->_model->getExtractedData('key'));
        $this->assertSame(null, $this->_model->getExtractedData('bad key'));
    }

    /**
     * @param $input
     * @param $output
     * @dataProvider applyInputFilterProvider
     */
    public function testApplyInputFilter($input, $output, $filter)
    {
        if ($input) {
            $this->_attributeMock
                ->expects($this->once())
                ->method('getInputFilter')
                ->will($this->returnValue($filter));
        }
        $this->assertEquals($output, $this->_model->applyInputFilter($input));
    }

    public function applyInputFilterProvider()
    {
        return [
            [false, false, false],
            [true, true, false],
            ['string', 'string', false],
            ['2014/01/23', '2014-01-23', 'date'],
            ['<tag>internal text</tag>', 'internal text', 'striptags']
        ];
    }

    /**
     * @param $format
     * @param $output
     * @dataProvider dataFilterFormatProvider
     */
    public function testDateFilterFormat($format, $output)
    {
        if (self::MODEL == $output) {
            $output = $this->_model;
        }
        if (is_null($format)) {
            $this->_localeMock
                ->expects($this->once())
                ->method('getDateFormat')
                ->with($this->equalTo(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT))
                ->will($this->returnValue($output));
        }
        $actual = $this->_model->dateFilterFormat($format);
        $this->assertEquals($output, $actual);
    }

    public function dataFilterFormatProvider()
    {
        return [
            [null, 'Whatever I put'],
            [false, self::MODEL],
            ['something else', self::MODEL]
        ];
    }

    /**
     * @param $input
     * @param $output
     * @dataProvider applyOutputFilterProvider
     */
    public function testApplyOutputFilter($input, $output, $filter)
    {
        if ($input) {
            $this->_attributeMock
                ->expects($this->once())
                ->method('getInputFilter')
                ->will($this->returnValue($filter));
        }
        $this->assertEquals($output, $this->_model->applyOutputFilter($input));
    }

    /**
     * This is similar to applyInputFilterProvider except for striptags
     *
     * @return array
     */
    public function applyOutputFilterProvider()
    {
        return [
            [false, false, false],
            [true, true, false],
            ['string', 'string', false],
            ['2014/01/23', '2014-01-23', 'date'],
            ['internal text', 'internal text', 'striptags']
        ];
    }

    /**
     * @param $value
     * @param $label
     * @param $inputValidation
     * @param $expectedOutput
     * @dataProvider validateInputRuleProvider
     */
    public function testValidateInputRule($value, $label, $inputValidation, $expectedOutput)
    {
        $this->_attributeMock
            ->expects($this->any())
            ->method('getStoreLabel')
            ->will($this->returnValue($label));
        $this->_attributeMock
            ->expects($this->any())
            ->method('getValidationRules')
            ->will($this->returnValue(['input_validation' => $inputValidation]));

        $this->assertEquals($expectedOutput, $this->_model->validateInputRule($value));
    }

    public function validateInputRuleProvider()
    {
        return [
            [null, null, null, true],
            ['value', null, null, true],
            [
                '!@#$',
                'mylabel',
                'alphanumeric',
                [\Zend_Validate_Alnum::NOT_ALNUM => '"mylabel" contains non-alphabetic or non-numeric characters.']
            ],
            [
                '!@#$',
                'mylabel',
                'numeric',
                [\Zend_Validate_Digits::NOT_DIGITS => '"mylabel" contains non-numeric characters.']
            ],
            [
                '1234',
                'mylabel',
                'alpha',
                [\Zend_Validate_Alpha::NOT_ALPHA => '"mylabel" contains non-alphabetic characters.']
            ],
            [
                '!@#$',
                'mylabel',
                'email',
                [
                    // @codingStandardsIgnoreStart
                    \Zend_Validate_EmailAddress::INVALID_HOSTNAME => '"mylabel" is not a valid hostname.',
                    \Zend_Validate_Hostname::INVALID_HOSTNAME     => "'#\$' does not match the expected structure for a DNS hostname",
                    \Zend_Validate_Hostname::INVALID_LOCAL_NAME   => "'#\$' does not appear to be a valid local network name."
                    // @codingStandardsIgnoreEnd
                ]
            ],
            [
                '1234',
                'mylabel',
                'url',
                ['"mylabel" is not a valid URL.']
            ],
            [
                'http://.com',
                'mylabel',
                'url',
                ['"mylabel" is not a valid URL.']
            ],
            [
                '1234',
                'mylabel',
                'date',
                [\Zend_Validate_Date::INVALID_DATE => '"mylabel" is not a valid date.']
            ],
        ];
    }

    /**
     * @param $ajaxRequest
     * @dataProvider trueFalseProvider
     */
    public function testGetIsAjaxRequest($ajaxRequest)
    {
        $this->_model = new ExtendsAbstractData(
            $this->_localeMock,
            $this->_loggerMock,
            $this->_attributeMock,
            $this->_value,
            $this->_entityTypeCode,
            $ajaxRequest
        );
        $this->assertSame($ajaxRequest, $this->_model->getIsAjaxRequest());
    }

    /**
     * @param $request
     * @param $attributeCode
     * @param $requestScope
     * @param $requestScopeOnly
     * @param $expectedValue
     * @dataProvider getRequestValueProvider
     */
    public function testGetRequestValue($request, $attributeCode, $requestScope, $requestScopeOnly, $expectedValue)
    {
        $this->_attributeMock
            ->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue($attributeCode));
        $this->_model->setRequestScope($requestScope);
        $this->_model->setRequestScopeOnly($requestScopeOnly);
        $this->assertEquals($expectedValue, $this->_model->getRequestValue($request));
    }

    public function getRequestValueProvider()
    {
        $expectedValue = 'expected value';
        $requestMockOne = $this->getMockBuilder('\Magento\App\RequestInterface')
            ->getMock();
        $requestMockOne->expects($this->any())
            ->method('getParam')
            ->with('ATTR_CODE')
            ->will($this->returnValue($expectedValue));

        $requestMockTwo = $this->getMockBuilder('\Magento\App\RequestInterface')
            ->getMock();
        $requestMockTwo->expects($this->at(0))
            ->method('getParam')
            ->with('request scope')
            ->will($this->returnValue(['ATTR_CODE' => $expectedValue]));
        $requestMockTwo->expects($this->at(1))
            ->method('getParam')
            ->with('request scope')
            ->will($this->returnValue([]));

        $requestMockThree = $this->getMockBuilder('\Magento\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $requestMockThree->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue(['request' => ['scope' => ['ATTR_CODE' => $expectedValue]]]));
        return [
            [$requestMockOne, 'ATTR_CODE', false, false, $expectedValue],
            [$requestMockTwo, 'ATTR_CODE', 'request scope', false, $expectedValue],
            [$requestMockTwo, 'ATTR_CODE', 'request scope', false, false],
            [$requestMockThree, 'ATTR_CODE', 'request/scope', false, $expectedValue],
        ];
    }
}