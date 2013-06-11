<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Saas_ImportExport_Model_Import_Image_Archive_ExtractorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_ImportExport_Helper_Import_Image_Configuration|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configurationMock;

    /**
     * @var Saas_ImportExport_Model_Import_Image_Archive_Adapter_Zip|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapterZipMock;

    /**
     * @var Varien_Data_Collection_FilesystemFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionFilesystemFactoryMock;

    /**
     * @var Saas_ImportExport_Model_Import_Image_FileSystem|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    /**
     * @var Saas_ImportExport_Model_Import_Image_Archive_Extractor
     */
    protected $_extractor;

    public static function setUpBeforeClass()
    {
        require_once __DIR__ . '/_files/ZipArchive.php';
    }

    public function setUp()
    {
        $this->_configurationMock = $this->getMock('Saas_ImportExport_Helper_Import_Image_Configuration', array(),
            array(), '', false);
        $this->_adapterZipMock = $this->getMock('Saas_ImportExport_Model_Import_Image_Archive_Adapter_Zip',
            array(), array(), '', false);
        $this->_collectionFilesystemFactoryMock = $this->getMock('Varien_Data_Collection_FilesystemFactory',
            array('create'), array(), '', false);
        $this->_filesystemMock = $this->getMock('Saas_ImportExport_Model_Import_Image_FileSystem', array(),
            array(), '', false);

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_extractor = $objectManager->getObject('Saas_ImportExport_Model_Import_Image_Archive_Extractor', array(
            'configuration' => $this->_configurationMock,
            'adapter' => $this->_adapterZipMock,
            'collectionFilesystemFactory' => $this->_collectionFilesystemFactoryMock,
            'fileSystem' => $this->_filesystemMock,
        ));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unsupported file type! Only ZIP file archives allowed.
     */
    public function testFailOpenFile()
    {
        $path = 'some path';
        $this->_adapterZipMock->expects($this->once())->method('open')->with($path)->will($this->returnValue(false));

        $this->_extractor->extract($path);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Error while extracting images.
     */
    public function testFailExtractFile()
    {
        $path = 'some path';
        $workingUnZipDir = 'unzip dir';

        $this->_configurationMock->expects($this->once())->method('getWorkingUnZipDir')
            ->will($this->returnValue($workingUnZipDir));

        $this->_adapterZipMock->expects($this->once())->method('open')->with($path)->will($this->returnValue(true));
        $this->_adapterZipMock->expects($this->once())->method('extractTo')->with($workingUnZipDir)
            ->will($this->returnValue(false));
        $this->_adapterZipMock->expects($this->once())->method('close');

        $this->_extractor->extract($path);
    }

    public function testExtractFailWithoutDeletingArchive()
    {
        $path = 'some path';
        $workingUnZipDir = 'unzip dir';

        $this->_configurationMock->expects($this->once())->method('getWorkingUnZipDir')
            ->will($this->returnValue($workingUnZipDir));

        $this->_adapterZipMock->expects($this->once())->method('open')->with($path)->will($this->returnValue(true));
        $this->_adapterZipMock->expects($this->once())->method('extractTo')->with($workingUnZipDir)
            ->will($this->returnValue(true));
        $this->_adapterZipMock->expects($this->once())->method('close');

        $this->_filesystemMock->expects($this->never())->method('removeFile');

        $this->_extractor->extract($path, false);
    }

    public function testExtractFailWithDeletingArchive()
    {
        $path = 'some path';
        $workingUnZipDir = 'unzip dir';

        $this->_configurationMock->expects($this->once())->method('getWorkingUnZipDir')
            ->will($this->returnValue($workingUnZipDir));

        $this->_adapterZipMock->expects($this->once())->method('open')->with($path)->will($this->returnValue(true));
        $this->_adapterZipMock->expects($this->once())->method('extractTo')->with($workingUnZipDir)
            ->will($this->returnValue(true));
        $this->_adapterZipMock->expects($this->once())->method('close');

        $this->_filesystemMock->expects($this->once())->method('removeFile')->with($path);

        $this->_extractor->extract($path);
    }

    public function testGetFiles()
    {
        $workingUnZipDir = 'unzip dir';
        $files = array('items' => array('file1', 'file2'));

        $this->_configurationMock->expects($this->once())->method('getWorkingUnZipDir')
            ->will($this->returnValue($workingUnZipDir));

        $collectionMock = $this->getMock('Varien_Data_Collection_Filesystem', array(), array(), '', false);
        $collectionMock->expects($this->once())->method('addTargetDir')->with($workingUnZipDir)
            ->will($this->returnSelf());
        $collectionMock->expects($this->once())->method('setCollectDirs')->with(false)->will($this->returnSelf());
        $collectionMock->expects($this->once())->method('setCollectFiles')->with(true)->will($this->returnSelf());
        $collectionMock->expects($this->once())->method('setFilesFilter')->with(false)->will($this->returnSelf());
        $collectionMock->expects($this->once())->method('setCollectRecursively')->with(true)->will($this->returnSelf());
        $collectionMock->expects($this->once())->method('toArray')->will($this->returnValue($files));

        $this->_collectionFilesystemFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($collectionMock));

        $this->assertEquals($files['items'], $this->_extractor->getFiles());
    }
}
