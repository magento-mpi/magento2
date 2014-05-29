<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
namespace Magento\Framework\Less\PreProcessor\Instruction;

use Magento\Framework\Filesystem\FilesystemException;

class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sample @import instructions.
     */
    const SAMPLE_IMPORT_SIMPLE = '@import "foo";';
    const SAMPLE_IMPORT_COMPLEX = '@import (reference) "foo" (image);';
    const SAMPLE_IMPORT_MALFORMED = '@this "is a malformed" (input);';

    /** @var  \Magento\Framework\View\RelatedFile | PHPUnit_Framework_MockObject */
    private $relatedFileMock;

    /** @var  \Magento\Framework\Less\PreProcessor\ErrorHandlerInterface | PHPUnit_Framework_MockObject */
    private $errorHandlerMock;

    /** @var  \Magento\Framework\Less\PreProcessor\File\FileList | PHPUnit_Framework_MockObject */
    private $lessFileListMock;

    /** @var  \Magento\Framework\Less\PreProcessor\File\Less | PHPUnit_Framework_MockObject */
    private $lessFileMock;

    /** @var  \Magento\Framework\Less\PreProcessor\Instruction\Import */
    private $importObject;

    public function setUp()
    {
        $this->relatedFileMock =
            $this->getMockBuilder('\Magento\Framework\View\RelatedFile')
            ->disableOriginalConstructor()
            ->getMock();

        $this->errorHandlerMock =
            $this->getMockBuilder('\Magento\Framework\Less\PreProcessor\ErrorHandlerInterface')
            ->getMock();

        $this->lessFileListMock =
            $this->getMockBuilder('\Magento\Framework\Less\PreProcessor\File\FileList')
            ->disableOriginalConstructor()
            ->getMock();

        $this->lessFileMock =
            $this->getMockBuilder('\Magento\Framework\Less\PreProcessor\File\Less')
            ->disableOriginalConstructor()
            ->getMock();

        $this->lessFileMock
            ->expects($this->any())
            ->method('getFilePath')
            ->will($this->returnValue('/'));

        $this->lessFileListMock
            ->expects($this->any())
            ->method('createFile')
            ->will($this->returnValue($this->lessFileMock));

        $this->importObject = new Import(
            $this->relatedFileMock,
            $this->errorHandlerMock,
            $this->lessFileListMock
        );
    }

    public function testProcessBlankRequestNoReplacement()
    {
        $expectedValue = '';

        $this->lessFileMock
            ->expects($this->never())
            ->method('getViewParams');

        $actualValueBlankRequest = $this->importObject
            ->process($this->lessFileMock, '');
        $this->assertEquals($expectedValue, $actualValueBlankRequest);

    }

    public function testProcessSimpleRequestNoReplacement()
    {
        $expectedValue = '';

        $this->lessFileMock
            ->expects($this->once())
            ->method('getViewParams');

        $this->lessFileListMock
            ->expects($this->once())
            ->method('createFile')
            ->with(null, null);

        $this->lessFileListMock
            ->expects($this->once())
            ->method('addFile');

        $actualValueSimpleRequestNoReplacement = $this->importObject
            ->process($this->lessFileMock, self::SAMPLE_IMPORT_SIMPLE);
        $this->assertEquals($expectedValue, $actualValueSimpleRequestNoReplacement);
    }

    public function testProcessSimpleRequestSingleReplacement()
    {
        $expectedValue = "@import 'bar';";

        $this->lessFileMock
            ->expects($this->once())
            ->method('getPublicationPath')
            ->will($this->returnValue('bar'));

        $actualValueSimpleReplacement = $this->importObject
            ->process($this->lessFileMock, self::SAMPLE_IMPORT_SIMPLE);

        $this->assertEquals($expectedValue, $actualValueSimpleReplacement);
    }

    public function testProcessComplexRequestSingleReplacement()
    {
        $expectedValue = "@import (reference) 'bar'  (image);";

        $this->lessFileMock
            ->expects($this->once())
            ->method('getPublicationPath')
            ->will($this->returnValue('bar'));

        $actualValueComplexReplacement = $this->importObject
            ->process($this->lessFileMock, self::SAMPLE_IMPORT_COMPLEX);

        $this->assertEquals($expectedValue, $actualValueComplexReplacement);
    }

    public function testMalformedRequest()
    {
        $expectedValue = self::SAMPLE_IMPORT_MALFORMED;

        $this->lessFileMock
            ->expects($this->never())
            ->method('getViewParams');

        $actualValueMalformedRequest = $this->importObject
            ->process($this->lessFileMock, self::SAMPLE_IMPORT_MALFORMED);

        $this->assertEquals($expectedValue, $actualValueMalformedRequest);
    }

    public function testProcessException()
    {
        $expectedValue = '';

        $this->lessFileMock
            ->expects($this->once())
            ->method('getPublicationPath')
            ->will($this->throwException(new FilesystemException));

        $actualValueExceptionRequest = $this->importObject
            ->process($this->lessFileMock, self::SAMPLE_IMPORT_SIMPLE);

        $this->assertEquals($expectedValue, $actualValueExceptionRequest);
    }

}