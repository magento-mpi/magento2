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
     * @var PHPUnit_Framework_MockObject_MockBuilder
     */
    protected $_modelBuilder;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_uploader;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    protected function setUp()
    {
        $this->_uploader = $this->getMock('Magento_Core_Model_File_Uploader', array(), array(), '', false);
        $uploaderFactory = $this->getMock(
            'Magento_Core_Model_File_UploaderFactory', array('create'), array(), '', false
        );
        $uploaderFactory->expects($this->any())->method('create')->will($this->returnValue($this->_uploader));
        $this->_filesystemMock = $this->getMock('Magento_Io_File', array('read'), array(), '', false);
        /** @var $service Magento_Theme_Model_Uploader_Service */
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments(
            'Magento_Theme_Model_Uploader_Service',
            array('fileIo' => $this->_filesystemMock, 'uploaderFactory' => $uploaderFactory)
        );
        $this->_modelBuilder = $this->getMockBuilder('Magento_Theme_Model_Uploader_Service')
            ->setConstructorArgs($arguments);
    }

    protected function tearDown()
    {
        $this->_modelBuilder = null;
        $this->_uploader = null;
    }

    public function testGetCssUploadMaxSize()
    {
        /** @var $service Magento_Theme_Model_Uploader_Service|PHPUnit_Framework_MockObject_MockObject */
        $service = $this->_modelBuilder->setMethods(array('_getMaxUploadSize'))->getMock();
        $service->expects($this->once())
            ->method('_getMaxUploadSize')
            ->with(Magento_Theme_Model_Uploader_Service::XML_PATH_CSS_UPLOAD_LIMIT)
            ->will($this->returnValue('5M'));

        $this->assertEquals('5M', $service->getCssUploadMaxSize());
    }

    public function testGetJsUploadMaxSize()
    {
        /** @var $service Magento_Theme_Model_Uploader_Service|PHPUnit_Framework_MockObject_MockObject */
        $service = $this->_modelBuilder->setMethods(array('_getMaxUploadSize'))->getMock();
        $service->expects($this->once())
            ->method('_getMaxUploadSize')
            ->with(Magento_Theme_Model_Uploader_Service::XML_PATH_JS_UPLOAD_LIMIT)
            ->will($this->returnValue('3M'));

        $this->assertEquals('3M', $service->getJsUploadMaxSize());
    }

    public function testGetFileContent()
    {
        $fileName = 'file.name';
        /** @var $service Magento_Theme_Model_Uploader_Service|PHPUnit_Framework_MockObject_MockObject */
        $service = $this->_modelBuilder->setMethods(null)->getMock();

        $this->_filesystemMock->expects($this->once())->method('read')->with($fileName)
            ->will($this->returnValue('content from my file'));

        $this->assertEquals('content from my file', $service->getFileContent($fileName));
    }

    public function testUploadCssFile()
    {
        $fileName = 'file.name';

        /** @var $service Magento_Theme_Model_Uploader_Service|PHPUnit_Framework_MockObject_MockObject */
        $service = $this->_modelBuilder->setMethods(array('_getMaxUploadSize'))->getMock();

        $service->expects($this->once())
            ->method('_getMaxUploadSize')
            ->with(Magento_Theme_Model_Uploader_Service::XML_PATH_CSS_UPLOAD_LIMIT)
            ->will($this->returnValue('5'));

        $this->_uploader->expects($this->once())
            ->method('getFileSize')
            ->will($this->returnValue('3'));

        $this->_filesystemMock->expects($this->once())->method('read')->with($fileName)
            ->will($this->returnValue('content'));

        $this->_uploader->expects($this->once())
            ->method('validateFile')
            ->will($this->returnValue(array('name' => $fileName, 'tmp_name' => $fileName)));

        $this->assertEquals(
            array('content' => 'content', 'filename' => $fileName),
            $service->uploadCssFile($fileName)
        );
    }

    /**
     * @expectedException Magento_Core_Exception
     */
    public function testUploadInvalidCssFile()
    {
        $fileName = 'file.name';
        /** @var $service Magento_Theme_Model_Uploader_Service|PHPUnit_Framework_MockObject_MockObject */
        $service = $this->_modelBuilder->setMethods(array('_getMaxUploadSize', 'getCssUploadMaxSizeInMb'))->getMock();

        $service->expects($this->once())
            ->method('_getMaxUploadSize')
            ->with(Magento_Theme_Model_Uploader_Service::XML_PATH_CSS_UPLOAD_LIMIT)
            ->will($this->returnValue('10'));

        $service->expects($this->once())
            ->method('getCssUploadMaxSizeInMb')
            ->will($this->returnValue('10'));

        $this->_uploader->expects($this->once())
            ->method('getFileSize')
            ->will($this->returnValue('30'));

        $service->uploadCssFile($fileName);
    }

    public function testUploadJsFile()
    {
        $fileName = 'file.name';

        /** @var $service Magento_Theme_Model_Uploader_Service|PHPUnit_Framework_MockObject_MockObject */
        $service = $this->_modelBuilder->setMethods(array('_getMaxUploadSize'))->getMock();

        $service->expects($this->once())
            ->method('_getMaxUploadSize')
            ->with(Magento_Theme_Model_Uploader_Service::XML_PATH_JS_UPLOAD_LIMIT)
            ->will($this->returnValue('500'));

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
            $service->uploadJsFile($fileName)
        );
    }

    /**
     * @expectedException Magento_Core_Exception
     */
    public function testUploadInvalidJsFile()
    {
        $fileName = 'file.name';

        /** @var $service Magento_Theme_Model_Uploader_Service|PHPUnit_Framework_MockObject_MockObject */
        $service = $this->_modelBuilder
            ->setMethods(array('_getMaxUploadSize', 'getFileContent', 'getJsUploadMaxSizeInMb'))
            ->getMock();

        $service->expects($this->once())
            ->method('_getMaxUploadSize')
            ->with(Magento_Theme_Model_Uploader_Service::XML_PATH_JS_UPLOAD_LIMIT)
            ->will($this->returnValue('100'));

        $service->expects($this->once())
            ->method('getJsUploadMaxSizeInMb')
            ->will($this->returnValue('499'));

        $this->_uploader->expects($this->once())
            ->method('getFileSize')
            ->will($this->returnValue('499'));

        $service->uploadJsFile($fileName);
    }
}
