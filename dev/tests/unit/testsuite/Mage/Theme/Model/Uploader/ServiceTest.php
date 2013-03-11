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
    public function testGetCssUploadMaxSize()
    {
        /** @var $service Mage_Theme_Model_Uploader_Service */
        $service = $this->getMock('Mage_Theme_Model_Uploader_Service', array('_getMaxUploadSize'), array(), '', false);

        $service->expects($this->once())
            ->method('_getMaxUploadSize')
            ->with(Mage_Theme_Model_Uploader_Service::XML_PATH_CSS_UPLOAD_LIMIT)
            ->will($this->returnValue('5M'));

        $this->assertEquals('5M', $service->getCssUploadMaxSize());
    }

    public function testGetJsUploadMaxSize()
    {
        /** @var $service Mage_Theme_Model_Uploader_Service */
        $service = $this->getMock('Mage_Theme_Model_Uploader_Service', array('_getMaxUploadSize'), array(), '', false);

        $service->expects($this->once())
            ->method('_getMaxUploadSize')
            ->with(Mage_Theme_Model_Uploader_Service::XML_PATH_JS_UPLOAD_LIMIT)
            ->will($this->returnValue('3M'));

        $this->assertEquals('3M', $service->getJsUploadMaxSize());
    }

    public function testGetFileContent()
    {
        $fileName = 'file.name';
        /** @var $filesystemMock Varien_Io_File */
        $filesystemMock = $this->getMock('Varien_Io_File', array('read'), array(), '', false, false);
        /** @var $service Mage_Theme_Model_Uploader_Service */
        $service = $this->getMock('Mage_Theme_Model_Uploader_Service', array('getFilePath'), array(), '', false);

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

        $this->assertEquals('content from my file', $service->getFileContent());
    }
}
