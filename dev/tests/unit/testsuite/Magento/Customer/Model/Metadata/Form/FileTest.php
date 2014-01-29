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

use Magento\Customer\Model\Metadata\ElementFactory;

class FileTest extends AbstractFormTestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Core\Helper\Data */
    protected $coreDataMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Core\Model\File\Validator\NotProtectedExtension */
    protected $fileValidatorMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Filesystem */
    protected $fileSystemMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\App\RequestInterface */
    protected $requestMock;

    protected function setUp()
    {
        parent::setUp();
        $this->coreDataMock = $this->getMockBuilder('Magento\Core\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->fileValidatorMock = $this->getMockBuilder('Magento\Core\Model\File\Validator\NotProtectedExtension')
            ->disableOriginalConstructor()
            ->getMock();
        $this->fileSystemMock = $this->getMockBuilder('Magento\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder('Magento\App\RequestInterface')
            ->disableOriginalConstructor()
            ->setMethods(
                ['getParam', 'getParams', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName']
            )
            ->getMock();
    }

    /**
     * @param array|bool $expected
     * @param string $attributeCode
     * @param bool $isAjax
     * @param string $delete
     * @dataProvider extractValueNoRequestScopeDataProvider
     */
    public function testExtractValueNoRequestScope($expected, $attributeCode = '', $isAjax = false, $delete = '')
    {
        $value = 'value';
        $fileForm = $this->getClass($value, $isAjax);

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->will($this->returnValue(['delete' => $delete]));

        $this->attributeMetadataMock->expects($this->any())
            ->method('getAttributeCode')
            ->will($this->returnValue($attributeCode));
        if (!empty($attributeCode)) {
            $_FILES[$attributeCode] = ['attributeCodeValue'];
        }
        $this->assertEquals($expected, $fileForm->extractValue($this->requestMock));
        if (!empty($attributeCode)) {
            unset($_FILES[$attributeCode]);
        }
    }

    public function extractValueNoRequestScopeDataProvider()
    {
        return [
            'ajax' => [false, '', true],
            'no_file' => [[],],
            'delete' => [['delete' => true], '', false, true],
            'file_delete' => [
                ['attributeCodeValue', 'delete' => true],
                'attributeCode',
                false,
                true
            ],
            'file_!delete' => [
                ['attributeCodeValue'],
                'attributeCode',
                false,
                false
            ],
        ];
    }

    /**
     * @param array $expected
     * @param string $requestScope
     * @param $mainScope
     * @dataProvider extractValueWithRequestScopeDataProvider
     */
    public function testExtractValueWithRequestScope($expected, $requestScope, $mainScope = false)
    {
        $value = 'value';
        $fileForm = $this->getClass($value, false);

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->will($this->returnValue(['delete' => true]));
        $this->requestMock->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(['delete' => true]));

        $this->attributeMetadataMock->expects($this->any())
            ->method('getAttributeCode')
            ->will($this->returnValue('attributeCode'));

        $fileForm->setRequestScope($requestScope);

        if ($mainScope) {
            $_FILES['mainScope'] = $mainScope;
        }
        $this->assertEquals($expected, $fileForm->extractValue($this->requestMock));
        if ($mainScope) {
            unset($_FILES['mainScope']);
        }
    }

    public function extractValueWithRequestScopeDataProvider()
    {
        return [
            'requestScope' => [[], 'requestScope'],
            'mainScope' => [
                ['fileKey' => 'attributeValue'],
                'mainScope',
                ['fileKey' => ['attributeCode' => 'attributeValue']],
            ],
            'mainScope/scopeName' => [
                ['fileKey' => 'attributeValue'],
                'mainScope/scopeName',
                ['fileKey' => ['scopeName' => ['attributeCode' => 'attributeValue']]],
            ],
        ];
    }

    /**
     * @param array|bool $expected
     * @param array $value
     * @param bool $isAjax
     * @param bool $isRequired
     * @dataProvider validateValueNotToUploadDataProvider
     */
    public function testValidateValueNotToUpload($expected, $value, $isAjax = false, $isRequired = true)
    {
        $fileForm = $this->getClass($value, $isAjax);
        $this->attributeMetadataMock->expects($this->any())
            ->method('isRequired')
            ->will($this->returnValue($isRequired));
        $this->attributeMetadataMock->expects($this->any())
            ->method('getStoreLabel')
            ->will($this->returnValue('attributeLabel'));

        $this->assertEquals($expected, $fileForm->validateValue($value));
    }

    public function validateValueNotToUploadDataProvider()
    {
        return [
            'emptyValue' => [true, [], true],
            'someValue' => [true, ['some value']],
            'delete_someValue' => [true, ['delete' => true, 'some value'], false, false],
            'null' => [['"attributeLabel" is a required value.'], null]
        ];
    }

    /**
     * @param array $expected
     * @param array $value
     * @param bool $isValid
     * @dataProvider validateValueToUploadDataProvider
     */
    public function testValidateValueToUpload($expected, $value, $isValid = false)
    {
        $fileForm = $this->getClass($value, false);
        $this->attributeMetadataMock->expects($this->any())
            ->method('isRequired')
            ->will($this->returnValue(false));
        $this->attributeMetadataMock->expects($this->any())
            ->method('getStoreLabel')
            ->will($this->returnValue('attributeLabel'));

        $this->fileValidatorMock->expects($this->any())
            ->method('getMessages')
            ->will($this->returnValue(['messages']));
        $this->fileValidatorMock->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue($isValid));
        $this->assertEquals($expected, $fileForm->validateValue($value));
    }

    public function validateValueToUploadDataProvider()
    {
        return [
            'notValid' => [['messages'], ['tmp_name' => 'file', 'name' => 'name']],
            'isValid' => [
                ['"attributeLabel" is not a valid file.'],
                ['tmp_name' => 'file', 'name' => 'name'],
                true
            ],
        ];
    }

    public function testCompactValueIsAjax()
    {
        $fileForm = $this->getClass('value', true);
        $this->assertSame($fileForm, $fileForm->compactValue('aValue'));
    }

    /**
     * @param string $expected
     * @param array $value
     * @dataProvider compactValueDataProvider
     */
    public function testCompactValue($expected, $value)
    {
        $fileForm = $this->getClass('value', false);
        $this->attributeMetadataMock->expects($this->any())
            ->method('isRequired')
            ->will($this->returnValue(false));
        $this->assertSame($expected, $fileForm->compactValue($value));
    }

    public function compactValueDataProvider()
    {
        return [
            'notDelete' => ['value', []],
            'delete' => ['', ['delete' => true]],
        ];
    }

    public function testRestoreValue()
    {
        $value = 'value';
        $fileForm = $this->getClass($value, false);
        $this->assertEquals($value, $fileForm->restoreValue('aValue'));
    }

    /**
     * @param string $format
     * @dataProvider outputValueDataProvider
     */
    public function testOutputValueNonJson($format)
    {
        $fileForm = $this->getClass('value', false);
        $this->assertSame('', $fileForm->outputValue($format));
    }

    public function outputValueDataProvider()
    {
        return [
            ElementFactory::OUTPUT_FORMAT_TEXT => [
                ElementFactory::OUTPUT_FORMAT_TEXT,
            ],
            ElementFactory::OUTPUT_FORMAT_ARRAY => [
                ElementFactory::OUTPUT_FORMAT_ARRAY,
            ],
            ElementFactory::OUTPUT_FORMAT_HTML => [
                ElementFactory::OUTPUT_FORMAT_HTML,
            ],
            ElementFactory::OUTPUT_FORMAT_ONELINE => [
                ElementFactory::OUTPUT_FORMAT_ONELINE,
            ],
            ElementFactory::OUTPUT_FORMAT_PDF => [
                ElementFactory::OUTPUT_FORMAT_PDF,
            ],
        ];
    }

    public function testOutputValueJson()
    {
        $value = 'value';
        $urlKey = 'url_key';
        $fileForm = $this->getClass($value, false);
        $this->coreDataMock->expects($this->once())
            ->method('urlEncode')
            ->with($this->equalTo($value))
            ->will($this->returnValue($urlKey));
        $expected = [
            'value' => $value,
            'url_key' => $urlKey,
        ];
        $this->assertSame(
            $expected,
            $fileForm->outputValue(ElementFactory::OUTPUT_FORMAT_JSON)
        );
    }

    /**
     * Helper for creating the unit under test.
     *
     * @param $isAjax
     * @param $value
     * @return File
     */
    protected function getClass($value, $isAjax)
    {
        $fileForm = new File(
            $this->localeMock,
            $this->loggerMock,
            $this->attributeMetadataMock,
            $value,
            0,
            $isAjax,
            $this->coreDataMock,
            $this->fileValidatorMock,
            $this->fileSystemMock
        );
        return $fileForm;
    }
}
