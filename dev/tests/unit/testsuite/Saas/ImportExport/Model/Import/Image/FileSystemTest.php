<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Saas_ImportExport_Model_Import_Image_FileSystemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configurationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileSystemMock;

    /**
     * @var Saas_ImportExport_Model_Import_Image_FileSystem
     */
    protected $_model;

    public function setUp()
    {
        $this->_configurationMock = $this->getMock('Saas_ImportExport_Helper_Import_Image_Configuration', array(),
            array(), '', false);
        $this->_fileSystemMock = $this->getMock('Magento_Filesystem', array(), array(), '', false);

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManager->getObject('Saas_ImportExport_Model_Import_Image_FileSystem', array(
            'configuration' => $this->_configurationMock,
            'fileSystem' => $this->_fileSystemMock,
        ));
    }

    public function testMoveFileToMedia()
    {
        $workingDir = 'working-dir' . DS;
        $mediaDir = 'media-dir' . DS;
        $file = 'some-file';

        $this->_configurationMock->expects($this->once())->method('getMediaDir')->will($this->returnValue($mediaDir));
        $this->_configurationMock->expects($this->once())->method('getWorkingUnZipDir')
            ->will($this->returnValue($workingDir));
        $this->_fileSystemMock->expects($this->once())->method('setIsAllowCreateDirectories')->with(true)
            ->will($this->returnSelf());
        $this->_fileSystemMock->expects($this->once())->method('ensureDirectoryExists')->with($mediaDir);
        $this->_fileSystemMock->expects($this->once())->method('rename')
            ->with($workingDir . DS . $file, $mediaDir . DS . $file);

        $this->_model->moveFileToMedia($workingDir . DS . $file);
    }

    public function testRemoveFile()
    {
        $file = 'some-file';

        $this->_fileSystemMock->expects($this->once())->method('delete')->with($file);

        $this->_model->removeFile($file);
    }
}
