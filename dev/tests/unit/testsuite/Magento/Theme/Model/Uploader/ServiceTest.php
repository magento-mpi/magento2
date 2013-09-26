<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for uploader service
 */
class Magento_Theme_Model_Uploader_ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Theme_Model_Uploader_Service
     */
    protected $_service;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_File_Uploader
     */
    protected $_uploader;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_File_UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_File_Size
     */
    protected $_fileSizeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Io_File
     */
    protected $_filesystemMock;

    /**
     * @var int
     */
    const MB_MULTIPLIER = 1048576;

    protected function setUp()
    {
        $this->_uploader = $this->getMock('Magento_Core_Model_File_Uploader', array(), array(), '', false);
        $this->_uploaderFactory = $this->getMock(
            'Magento_Core_Model_File_UploaderFactory', array('create'), array(), '', false
        );
        $this->_uploaderFactory->expects($this->any())->method('create')->will($this->returnValue($this->_uploader));
        $this->_filesystemMock = $this->getMock('Magento_Io_File', array('read'), array(), '', false);
        /** @var $service Magento_Theme_Model_Uploader_Service */

        $this->_fileSizeMock = $this->getMockBuilder('Magento_File_Size')
            ->setMethods(array('getMaxFileSize'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_fileSizeMock->expects($this->any())
            ->method('getMaxFileSize')
            ->will($this->returnValue(600 * self::MB_MULTIPLIER));
    }

    protected function tearDown()
    {
        $this->_service = null;
        $this->_uploader = null;
        $this->_fileSizeMock = null;
        $this->_filesystemMock = null;
        $this->_uploaderFactory = null;
    }

    public function testUploadLimitNotConfigured()
    {
        $this->_service = new Magento_Theme_Model_Uploader_Service(
            $this->_filesystemMock,
            $this->_fileSizeMock,
            $this->_uploaderFactory
        );
        $this->assertEquals(600 * self::MB_MULTIPLIER, $this->_service->getJsUploadMaxSize());
        $this->assertEquals(600 * self::MB_MULTIPLIER, $this->_service->getCssUploadMaxSize());
    }

    public function testGetCssUploadMaxSize()
    {
        $this->_service = new Magento_Theme_Model_Uploader_Service(
            $this->_filesystemMock,
            $this->_fileSizeMock,
            $this->_uploaderFactory,
            array(
                'css' => '5M'
            )
        );
        $this->assertEquals(5 * self::MB_MULTIPLIER, $this->_service->getCssUploadMaxSize());
    }

    public function testGetJsUploadMaxSize()
    {
        $this->_service = new Magento_Theme_Model_Uploader_Service(
            $this->_filesystemMock,
            $this->_fileSizeMock,
            $this->_uploaderFactory,
            array(
                'js' => '3M'
            )
        );
        $this->assertEquals(3 * self::MB_MULTIPLIER, $this->_service->getJsUploadMaxSize());
    }

    public function testGetFileContent()
    {
        $fileName = 'file.name';
        $this->_filesystemMock->expects($this->once())->method('read')->with($fileName)
            ->will($this->returnValue('content from my file'));
        $this->_service = new Magento_Theme_Model_Uploader_Service(
            $this->_filesystemMock,
            $this->_fileSizeMock,
            $this->_uploaderFactory,
            array(
                'js' => '3M'
            )
        );

        $this->assertEquals('content from my file', $this->_service->getFileContent($fileName));
    }

    public function testUploadCssFile()
    {
        $fileName = 'file.name';
        $this->_service = new Magento_Theme_Model_Uploader_Service(
            $this->_filesystemMock,
            $this->_fileSizeMock,
            $this->_uploaderFactory,
            array(
                'css' => '3M'
            )
        );

        $this->_filesystemMock->expects($this->once())->method('read')->with($fileName)
            ->will($this->returnValue('content'));

        $this->_uploader->expects($this->once())
            ->method('validateFile')
            ->will($this->returnValue(array('name' => $fileName, 'tmp_name' => $fileName)));

        $this->assertEquals(
            array('content' => 'content', 'filename' => $fileName),
            $this->_service->uploadCssFile($fileName)
        );
    }

    /**
     * @expectedException Magento_Core_Exception
     */
    public function testUploadInvalidCssFile()
    {
        $fileName = 'file.name';

        $this->_uploader->expects($this->once())
            ->method('getFileSize')
            ->will($this->returnValue(30 * self::MB_MULTIPLIER));

        $this->_service = new Magento_Theme_Model_Uploader_Service(
            $this->_filesystemMock,
            $this->_fileSizeMock,
            $this->_uploaderFactory,
            array(
                'css' => '10M'
            )
        );

        $this->_service->uploadCssFile($fileName);
    }

    public function testUploadJsFile()
    {
        $fileName = 'file.name';

        $this->_fileSizeMock->expects($this->once())
            ->method('getMaxFileSize')
            ->will($this->returnValue(600 * self::MB_MULTIPLIER));

        $this->_service = new Magento_Theme_Model_Uploader_Service(
            $this->_filesystemMock,
            $this->_fileSizeMock,
            $this->_uploaderFactory,
            array(
                'js' => '500M'
            )
        );

        $this->_filesystemMock->expects($this->once())->method('read')->with($fileName)
            ->will($this->returnValue('content'));

        $this->_uploader->expects($this->once())
            ->method('validateFile')
            ->will($this->returnValue(array('name' => $fileName, 'tmp_name' => $fileName)));

        $this->_uploader->expects($this->once())
            ->method('getFileSize')
            ->will($this->returnValue('499'));

        $this->assertEquals(
            array('content' => 'content', 'filename' => $fileName),
            $this->_service->uploadJsFile($fileName)
        );
    }

    /**
     * @expectedException Magento_Core_Exception
     */
    public function testUploadInvalidJsFile()
    {
        $fileName = 'file.name';
        $this->_service = new Magento_Theme_Model_Uploader_Service(
            $this->_filesystemMock,
            $this->_fileSizeMock,
            $this->_uploaderFactory,
            array(
                'js' => '100M'
            )
        );

        $this->_uploader->expects($this->once())
            ->method('getFileSize')
            ->will($this->returnValue(499 * self::MB_MULTIPLIER));

        $this->_service->uploadJsFile($fileName);
    }
}
