<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Storage model test
 */
class Mage_Theme_Model_Uploader_ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockBuilder
     */
    protected $_modelBuilder;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_uploader;

    protected function setUp()
    {
        $this->_uploader = $this->getMock('Mage_Core_Model_File_Uploader', array(), array(), '', false);
        $uploaderFactory = $this->getMock('Mage_Core_Model_File_UploaderFactory', array(), array(), '', false);
        $uploaderFactory->expects($this->any())->method('create')->will($this->returnValue($this->_uploader));
        /** @var $service Mage_Theme_Model_Uploader_Service */
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments(
            'Mage_Theme_Model_Uploader_Service', array('uploaderFactory' => $uploaderFactory)
        );
        $this->_modelBuilder = $this->getMockBuilder('Mage_Theme_Model_Uploader_Service')
            ->setConstructorArgs($arguments);
    }

    protected function tearDown()
    {
        $this->_modelBuilder = null;
        $this->_uploader = null;
    }

    public function testGetCssUploadMaxSize()
    {
        /** @var $service Mage_Theme_Model_Uploader_Service|PHPUnit_Framework_MockObject_MockObject */
        $service = $this->_modelBuilder->setMethods(array('_getMaxUploadSize'))->getMock();
        $service->expects($this->once())
            ->method('_getMaxUploadSize')
            ->with(Mage_Theme_Model_Uploader_Service::XML_PATH_CSS_UPLOAD_LIMIT)
            ->will($this->returnValue('5M'));

        $this->assertEquals('5M', $service->getCssUploadMaxSize());
    }

    public function testGetJsUploadMaxSize()
    {
        /** @var $service Mage_Theme_Model_Uploader_Service|PHPUnit_Framework_MockObject_MockObject */
        $service = $this->_modelBuilder->setMethods(array('_getMaxUploadSize'))->getMock();
        $service->expects($this->once())
            ->method('_getMaxUploadSize')
            ->with(Mage_Theme_Model_Uploader_Service::XML_PATH_JS_UPLOAD_LIMIT)
            ->will($this->returnValue('3M'));

        $this->assertEquals('3M', $service->getJsUploadMaxSize());
    }

    public function testGetFileContent()
    {
        $fileName = 'file.name';
        /** @var $filesystemMock Varien_Io_File|PHPUnit_Framework_MockObject_MockObject */
        $filesystemMock = $this->getMock('Varien_Io_File', array('read'), array(), '', false, false);
        /** @var $service Mage_Theme_Model_Uploader_Service|PHPUnit_Framework_MockObject_MockObject */
        $service = $this->_modelBuilder->setMethods(array('getFilePath'))->getMock();

        $service->expects($this->once())
            ->method('getFilePath')
            ->will($this->returnValue($fileName));

        $filesystemMock->expects($this->once())
            ->method('read')
            ->with($fileName)
            ->will($this->returnValue('content from my file'));

        $property = new ReflectionProperty($service, '_fileIo');
        $property->setAccessible(true);
        $property->setValue($service, $filesystemMock);

        $this->assertEquals('content from my file', $service->getFileContent($fileName));
    }

    public function testUploadCssFile()
    {
        $file['tmp_name'] = 'file.name';

        /** @var $service Mage_Theme_Model_Uploader_Service|PHPUnit_Framework_MockObject_MockObject */
        $service = $this->_modelBuilder->setMethods(array('_getMaxUploadSize'))->getMock();

        $service->expects($this->once())
            ->method('_getMaxUploadSize')
            ->with(Mage_Theme_Model_Uploader_Service::XML_PATH_CSS_UPLOAD_LIMIT)
            ->will($this->returnValue('5'));

        $this->_uploader->expects($this->once())
            ->method('getFileSize')
            ->will($this->returnValue('3'));

        $this->_uploader->expects($this->once())
            ->method('validateFile')
            ->will($this->returnValue($file));

        $this->assertEquals('file.name', $service->uploadCssFile($file));
    }

    /**
     * @expectedException Mage_Core_Exception
     */
    public function testUploadInvalidCssFile()
    {
        $file['tmp_name'] = 'file.name';
        /** @var $service Mage_Theme_Model_Uploader_Service|PHPUnit_Framework_MockObject_MockObject */
        $service = $this->_modelBuilder->setMethods(array('_getMaxUploadSize', 'getCssUploadMaxSizeInMb'))->getMock();

        $service->expects($this->once())
            ->method('_getMaxUploadSize')
            ->with(Mage_Theme_Model_Uploader_Service::XML_PATH_CSS_UPLOAD_LIMIT)
            ->will($this->returnValue('10'));

        $service->expects($this->once())
            ->method('getCssUploadMaxSizeInMb')
            ->will($this->returnValue('10'));

        $uploader = $this->getMock('Mage_Core_Model_File_Uploader', array(), array(), '', false);

        $uploader->expects($this->once())
            ->method('getFileSize')
            ->will($this->returnValue('30'));

        $this->assertEquals(array('filename' => 'file.name'), $service->uploadCssFile($file));
    }

    public function testUploadJsFile()
    {
        $file['tmp_name'] = 'file.name';

        /** @var $service Mage_Theme_Model_Uploader_Service|PHPUnit_Framework_MockObject_MockObject */
        $service = $this->_modelBuilder->setMethods(array('_getMaxUploadSize', 'getFileContent'))->getMock();

        $fileJs = $this->getMock(
            'Mage_Core_Model_Theme_Customization_File_Js', array(), array(), '', false
        );

        $fileJs->expects($this->once())
            ->method('saveJsFile')
            ->will($this->returnValue($file));

        $service->expects($this->once())
            ->method('_getMaxUploadSize')
            ->with(Mage_Theme_Model_Uploader_Service::XML_PATH_JS_UPLOAD_LIMIT)
            ->will($this->returnValue('500'));

        $service->expects($this->once())
            ->method('getFileContent')
            ->will($this->returnValue('Uploaded file content'));

        $uploader = $this->getMock('Mage_Core_Model_File_Uploader', array(), array(), '', false);

        $uploader->expects($this->once())
            ->method('getFileSize')
            ->will($this->returnValue('499'));

        $uploader->expects($this->once())
            ->method('validateFile')
            ->will($this->returnValue($file));

        $this->assertEquals(array('filename' => 'file.name'), $service->uploadJsFile($file));
    }

    /**
     * @expectedException Mage_Core_Exception
     */
    public function testUploadInvalidJsFile()
    {
        $file['tmp_name'] = 'file.name';

        $dataHelper = $this->getMock('Mage_Core_Helper_Data', array(), array(), '', false);

        /** @var $service Mage_Theme_Model_Uploader_Service|PHPUnit_Framework_MockObject_MockObject */
        $service = $this->_modelBuilder
            ->setMethods(array('_getMaxUploadSize', 'getFileContent', 'getJsUploadMaxSizeInMb'))
            ->getMock();

        $fileJs = $this->getMock(
            'Mage_Core_Model_Theme_Customization_File_Js', array(), array(), '', false
        );

        $service->expects($this->once())
            ->method('_getMaxUploadSize')
            ->with(Mage_Theme_Model_Uploader_Service::XML_PATH_JS_UPLOAD_LIMIT)
            ->will($this->returnValue('100'));

        $service->expects($this->once())
            ->method('getJsUploadMaxSizeInMb')
            ->will($this->returnValue('499'));

        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $uploader = $this->getMock('Mage_Core_Model_File_Uploader', array(), array(), '', false);

        $uploader->expects($this->once())
            ->method('getFileSize')
            ->will($this->returnValue('499'));

        $objectManager->expects($this->once())
            ->method('create')
            ->will($this->returnValue($uploader));

        $objectManager->expects($this->once())
            ->method('get')
            ->will($this->returnValue($dataHelper));

        $property = new ReflectionProperty($service, '_objectManager');
        $property->setAccessible(true);
        $property->setValue($service, $objectManager);

        $property = new ReflectionProperty($service, '_filesJs');
        $property->setAccessible(true);
        $property->setValue($service, $fileJs);

        $this->assertEquals(array('filename' => 'file.name'), $service->uploadJsFile($file));
    }
}
