<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
namespace Magento\Framework\Less\PreProcessor\Instruction;

class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sample @import instructions.
     */
    const SAMPLE_IMPORT_1 = '@import "foo";';
    const SAMPLE_IMPORT_2 = '@import (reference) "foo" (image);';

    /** @var  \Magento\Framework\View\RelatedFile | PHPUnit_Framework_MockObject */
    protected $relatedFileMock;

    /** @var  \Magento\Framework\Less\PreProcessor\ErrorHandlerInterface | PHPUnit_Framework_MockObject */
    protected $errorHandlerMock;

    /** @var  \Magento\Framework\Less\PreProcessor\File\FileList | PHPUnit_Framework_MockObject */
    protected $lessFileListMock;

    /** @var  \Magento\Framework\Less\PreProcessor\File\Less | PHPUnit_Framework_MockObject */
    protected $lessFileMock;

    /**
     * Setup tests
     * @return void
     */
    public function setUp()
    {
        $this->relatedFileMock = $this->getMockBuilder('\Magento\Framework\View\RelatedFile')
                                      ->disableOriginalConstructor()->getMock();
        $this->errorHandlerMock = $this->getMockBuilder('\Magento\Framework\Less\PreProcessor\ErrorHandlerInterface')
                                       ->getMock();
        $this->lessFileListMock = $this->getMockBuilder('\Magento\Framework\Less\PreProcessor\File\FileList')
                                       ->disableOriginalConstructor()->getMock();

        $this->lessFileMock = $this->getMockBuilder('\Magento\Framework\Less\PreProcessor\File\Less')
                                   ->disableOriginalConstructor()->getMock();

        $this->lessFileMock->expects($this->any())->method('getFilePath')->will($this->returnValue('/'));

        $this->lessFileListMock->expects($this->any())->method('createFile')
                               ->will($this->returnValue($this->lessFileMock));
    }

    /**
     * Tests base case of no matched files.
     * @return void
     */
    public function testProcess()
    {
        $importObject = new Import($this->relatedFileMock, $this->errorHandlerMock, $this->lessFileListMock);

        $this->assertEquals('', $importObject->process($this->lessFileMock, ''));

        $this->assertEquals('', $importObject->process($this->lessFileMock, self::SAMPLE_IMPORT_1));
    }

    /**
     * Tests simple case of a single path replacement.
     * @return void
     */
    public function testProcess2()
    {
        $importObject = new Import($this->relatedFileMock, $this->errorHandlerMock, $this->lessFileListMock);

        $this->lessFileMock->expects($this->any())->method('getPublicationPath')->will($this->returnValue('bar'));

        $this->assertEquals("@import 'bar';", $importObject->process($this->lessFileMock, self::SAMPLE_IMPORT_1));

        $expectedValue = "@import (reference) 'bar'  (image);";

        $this->assertEquals($expectedValue,
            $importObject->process($this->lessFileMock, self::SAMPLE_IMPORT_2)
        );

    }

    /**
     * Tests the exception path.
     * @return void
     */
    public function testProcessException()
    {
        $importObject = new Import($this->relatedFileMock, $this->errorHandlerMock, $this->lessFileListMock);

        $this->lessFileListMock->expects($this->any())->method('addFile')
                               ->will($this->throwException(new \Magento\Framework\Filesystem\FilesystemException));

        $this->lessFileMock->expects($this->any())->method('getPublicationPath')->will($this->returnValue('car'));

        $this->assertEquals('', $importObject->process($this->lessFileMock, self::SAMPLE_IMPORT_1));
    }

}