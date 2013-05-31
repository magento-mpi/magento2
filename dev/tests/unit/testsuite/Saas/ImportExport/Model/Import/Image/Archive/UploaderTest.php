<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Import_Image_Archive_UploaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configurationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_uploaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_uploaderFactoryMock;

    /**
     * @var Saas_ImportExport_Model_Import_Image_Archive_Uploader
     */
    protected $_uploader;

    public function setUp()
    {
        $this->_configurationMock = $this->getMock('Saas_ImportExport_Helper_Import_Image_Configuration', array(),
            array(), '', false);
        $this->_uploaderMock = $this->getMock('Varien_File_Uploader', array(), array(), '', false);
        $this->_uploaderFactoryMock = $this->getMock('Varien_File_UploaderFactory', array('create'), array(), '',
            false);

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_uploader = $objectManager->getObject('Saas_ImportExport_Model_Import_Image_Archive_Uploader', array(
            'configuration' => $this->_configurationMock,
            'uploaderFactory' => $this->_uploaderFactoryMock,
        ));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Image Archive upload error.
     */
    public function testFailUpload()
    {
        $fileId = 'some-fileId';
        $allowedExtensions = array('ext1', 'ext2', 'ext3');
        $workingDir = 'working dir';
        $result = false;

        $this->_configurationMock->expects($this->once())->method('getFileFieldName')
            ->will($this->returnValue($fileId));
        $this->_configurationMock->expects($this->once())->method('getArchiveAllowedExtensions')
            ->will($this->returnValue($allowedExtensions));
        $this->_configurationMock->expects($this->once())->method('getWorkingDir')
            ->will($this->returnValue($workingDir));

        $this->_uploaderFactoryMock->expects($this->once())->method('create')->with(array('fileId' => $fileId))
            ->will($this->returnValue($this->_uploaderMock));
        $this->_uploaderMock->expects($this->once())->method('setAllowedExtensions')->with($allowedExtensions);
        $this->_uploaderMock->expects($this->once())->method('save')->with($workingDir)
            ->will($this->returnValue($result));

        $this->_uploader->upload();
    }

    public function testUpload()
    {
        $fileId = 'some-fileId';
        $allowedExtensions = array('ext1', 'ext2', 'ext3');
        $workingDir = 'working dir';
        $result = array('path' => 'path', 'file' => 'file');

        $this->_configurationMock->expects($this->once())->method('getFileFieldName')
            ->will($this->returnValue($fileId));
        $this->_configurationMock->expects($this->once())->method('getArchiveAllowedExtensions')
            ->will($this->returnValue($allowedExtensions));
        $this->_configurationMock->expects($this->once())->method('getWorkingDir')
            ->will($this->returnValue($workingDir));

        $this->_uploaderFactoryMock->expects($this->once())->method('create')->with(array('fileId' => $fileId))
            ->will($this->returnValue($this->_uploaderMock));
        $this->_uploaderMock->expects($this->once())->method('setAllowedExtensions')->with($allowedExtensions);
        $this->_uploaderMock->expects($this->once())->method('save')->with($workingDir)
            ->will($this->returnValue($result));

        $this->assertEquals('pathfile', $this->_uploader->upload());
    }
}
