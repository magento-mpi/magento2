<?php
/**
 * Magento\Customer\Model\Metadata\Form\File
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata\Form;

class FileTest extends \PHPUnit_Framework_TestCase
{

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Core\Model\LocaleInterface */
    protected $_localeMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Logger */
    protected $_loggerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata */
    protected $_attributeMetadataMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Core\Helper\Data */
    protected $_coreDataMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Core\Model\File\Validator\NotProtectedExtension */
    protected $_fileValidatorMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Filesystem */
    protected $_fileSystemMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\App\RequestInterface */
    protected $_requestMock;

    protected function setUp()
    {
        $this->_localeMock = $this->getMockBuilder('Magento\Core\Model\LocaleInterface')->getMock();
        $this->_loggerMock = $this->getMockBuilder('Magento\Logger')->disableOriginalConstructor()->getMock();
        $this->_attributeMetadataMock = $this->getMockBuilder('Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_coreDataMock = $this->getMockBuilder('Magento\Core\Helper\Data')
        ->disableOriginalConstructor()
        ->getMock();
        $this->_fileValidatorMock = $this->getMockBuilder('Magento\Core\Model\File\Validator\NotProtectedExtension')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_fileSystemMock = $this->getMockBuilder('Magento\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_requestMock = $this->getMockBuilder('Magento\App\RequestInterface')
            ->disableOriginalConstructor()
            ->setMethods(
                ['getParam', 'getParams', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName']
            )
            ->getMock();
    }

    /**
     * @param $expected
     * @param string $attributeCode
     * @param bool $isAjax
     * @param string $delete
     * @dataProvider extractValueNoRequestScopeDataProvider
     */
    public function testExtractValueNoRequestScope($expected, $attributeCode = '', $isAjax = false, $delete = '')
    {
        $value = 'value';
        $fileForm = new File(
            $this->_localeMock,
            $this->_loggerMock,
            $this->_attributeMetadataMock,
            $value,
            0,
            $isAjax,
            $this->_coreDataMock,
            $this->_fileValidatorMock,
            $this->_fileSystemMock
        );

        $this->_requestMock->expects($this->any())
            ->method('getParam')
            ->will($this->returnValue(['delete' => $delete]));

        $this->_attributeMetadataMock->expects($this->any())
            ->method('getAttributeCode')
            ->will($this->returnValue($attributeCode));
        if (!empty($attributeCode)) {
            $_FILES[$attributeCode] = ['attributeCodeValue'];
        }
        $this->assertEquals($expected, $fileForm->extractValue($this->_requestMock));
        if (!empty($attributeCode)) {
            unset($_FILES[$attributeCode]);
        }
    }

    public function extractValueNoRequestScopeDataProvider()
    {
        return [
            '0' => [false, '', true],
            '1' => [[],],
            '2' => [['delete' => true], '', false, true],
            '3' => [
                ['attributeCodeValue', 'delete' => true],
                'attributeCode',
                false,
                true
            ],
            '4' => [
                ['attributeCodeValue'],
                'attributeCode',
                false,
                false
            ],
        ];
    }

    /**
     * @param $expected
     * @param string $requestScope
     * @param $mainScope
     * @dataProvider extractValueWithRequestScopeDataProvider
     */
    public function testExtractValueWithRequestScope($expected, $requestScope, $mainScope = false)
    {
        $value = 'value';
        $fileForm = new File(
            $this->_localeMock,
            $this->_loggerMock,
            $this->_attributeMetadataMock,
            $value,
            0,
            false,
            $this->_coreDataMock,
            $this->_fileValidatorMock,
            $this->_fileSystemMock
        );

        $this->_requestMock->expects($this->any())
            ->method('getParam')
            ->will($this->returnValue(['delete' => true]));
        $this->_requestMock->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(['delete' => true]));

        $this->_attributeMetadataMock->expects($this->any())
            ->method('getAttributeCode')
            ->will($this->returnValue('attributeCode'));

        $fileForm->setRequestScope($requestScope);

        if ($mainScope) {
            $_FILES['mainScope'] = $mainScope;
        }
        $this->assertEquals($expected, $fileForm->extractValue($this->_requestMock));
        if ($mainScope) {
            unset($_FILES['mainScope']);
        }
    }

    public function extractValueWithRequestScopeDataProvider()
    {
        return [
            '0' => [[], 'requestScope'],
            '1' => [
                ['fileKey' => 'attributeValue'],
                'mainScope',
                ['fileKey' => ['attributeCode' => 'attributeValue']],
            ],
            '2' => [
                ['fileKey' => 'attributeValue'],
                'mainScope/scopeName',
                ['fileKey' => ['scopeName' => ['attributeCode' => 'attributeValue']]],
            ],
        ];
    }

    /**
     * @param $expected
     * @param $value
     * @param bool $isAjax
     * @param bool $isRequired
     * @dataProvider validateValueNotToUploadDataProvider
     */
    public function testValidateValueNotToUpload($expected, $value, $isAjax = false, $isRequired = true)
    {
        $fileForm = new File(
            $this->_localeMock,
            $this->_loggerMock,
            $this->_attributeMetadataMock,
            $value,
            0,
            $isAjax,
            $this->_coreDataMock,
            $this->_fileValidatorMock,
            $this->_fileSystemMock
        );
        $this->_attributeMetadataMock->expects($this->any())
            ->method('isRequired')
            ->will($this->returnValue($isRequired));
        $this->_attributeMetadataMock->expects($this->any())
            ->method('getStoreLabel')
            ->will($this->returnValue('attributeLabel'));

        $this->assertEquals($expected, $fileForm->validateValue($value));
    }

    public function validateValueNotToUploadDataProvider()
    {
        return [
            '0' => [true, [], true],
            '1' => [true, ['some value']],
            '2' => [true, ['delete' => true, 'some value'], false, false],
            '3' => [['"attributeLabel" is a required value.'], null]
        ];
    }

    /**
     * @param $expected
     * @param $value
     * @param bool $isValid
     * @dataProvider validateValueToUploadDataProvider
     */
    public function testValidateValueToUpload($expected, $value, $isValid = false)
    {
        $fileForm = new File(
            $this->_localeMock,
            $this->_loggerMock,
            $this->_attributeMetadataMock,
            $value,
            0,
            false,
            $this->_coreDataMock,
            $this->_fileValidatorMock,
            $this->_fileSystemMock
        );
        $this->_attributeMetadataMock->expects($this->any())
            ->method('isRequired')
            ->will($this->returnValue(false));
        $this->_attributeMetadataMock->expects($this->any())
            ->method('getStoreLabel')
            ->will($this->returnValue('attributeLabel'));

        $this->_fileValidatorMock->expects($this->any())
            ->method('getMessages')
            ->will($this->returnValue(['messages']));
        $this->_fileValidatorMock->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue($isValid));
        $this->assertEquals($expected, $fileForm->validateValue($value));
    }

    public function validateValueToUploadDataProvider()
    {
        return [
            '0' => [['messages'], ['tmp_name' => 'file', 'name' => 'name']],
            '1' => [
                ['"attributeLabel" is not a valid file.'],
                ['tmp_name' => 'file', 'name' => 'name'],
                true
            ],
        ];
    }

    public function testCompactValueIsAjax()
    {
        $fileForm = new File(
            $this->_localeMock,
            $this->_loggerMock,
            $this->_attributeMetadataMock,
            'value',
            0,
            true,
            $this->_coreDataMock,
            $this->_fileValidatorMock,
            $this->_fileSystemMock
        );
        $this->assertSame($fileForm, $fileForm->compactValue('aValue'));
    }

    /**
     * @param $expected
     * @param $value
     * @dataProvider compactValueDataProvider
     */
    public function testCompactValue($expected, $value)
    {
        $fileForm = new File(
            $this->_localeMock,
            $this->_loggerMock,
            $this->_attributeMetadataMock,
            'value',
            0,
            false,
            $this->_coreDataMock,
            $this->_fileValidatorMock,
            $this->_fileSystemMock
        );
        $this->_attributeMetadataMock->expects($this->any())
            ->method('isRequired')
            ->will($this->returnValue(false));
        $this->assertSame($expected, $fileForm->compactValue($value));
    }

    public function compactValueDataProvider()
    {
        return [
            '0' => ['value', []],
            '1' => ['', ['delete' => true]],
        ];
    }

    public function testRestoreValue()
    {
        $value = 'value';
        $fileForm = new File(
            $this->_localeMock,
            $this->_loggerMock,
            $this->_attributeMetadataMock,
            $value,
            0,
            false,
            $this->_coreDataMock,
            $this->_fileValidatorMock,
            $this->_fileSystemMock
        );
        $this->assertEquals($value, $fileForm->restoreValue('aValue'));
    }

    /**
     * @param string $format
     * @dataProvider outputValueDataProvider
     */
    public function testOutputValueNonJson($format)
    {
        $fileForm = new File(
            $this->_localeMock,
            $this->_loggerMock,
            $this->_attributeMetadataMock,
            'value',
            0,
            false,
            $this->_coreDataMock,
            $this->_fileValidatorMock,
            $this->_fileSystemMock
        );
        $this->assertSame('', $fileForm->outputValue($format));
    }

    public function outputValueDataProvider()
    {
        return [
            \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_TEXT => [
                \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_TEXT,
            ],
            \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_ARRAY => [
                \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_ARRAY,
            ],
            \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_HTML => [
                \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_HTML,
            ],
            \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_ONELINE => [
                \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_ONELINE,
            ],
            \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_PDF => [
                \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_PDF,
            ],
        ];
    }

    public function testOutputValueJson()
    {
        $value = 'value';
        $urlKey = 'url_key';
        $fileForm = new File(
            $this->_localeMock,
            $this->_loggerMock,
            $this->_attributeMetadataMock,
            $value,
            0,
            false,
            $this->_coreDataMock,
            $this->_fileValidatorMock,
            $this->_fileSystemMock
        );
        $this->_coreDataMock->expects($this->once())
            ->method('urlEncode')
            ->with($this->equalTo($value))
            ->will($this->returnValue($urlKey));
        $expected = [
            'value' => $value,
            'url_key' => $urlKey,
        ];
        $this->assertSame(
            $expected,
            $fileForm->outputValue(\Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_JSON)
        );
    }
}
