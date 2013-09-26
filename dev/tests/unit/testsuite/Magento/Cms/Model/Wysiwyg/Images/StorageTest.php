<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Cms_Model_Wysiwyg_Images_StorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Cms_Model_Wysiwyg_Images_Storage
     */
    protected $_model = null;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewUrlMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapterFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_imageHelperMock;

    /**
     * @var array()
     */
    protected $_resizeParameters;

    /**
     * @var Magento_Core_Model_Dir|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    /**
     * @var Magento_Cms_Model_Wysiwyg_Images_Storage_CollectionFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageCollectionFactoryMock;

    /**
     * @var Magento_Core_Model_File_Storage_FileFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageFileFactoryMock;

    /**
     * @var Magento_Core_Model_File_Storage_DatabaseFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageDatabaseFactoryMock;

    /**
     * @var Magento_Core_Model_File_Storage_Directory_DatabaseFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_directoryDatabaseFactoryMock;

    /**
     * @var Magento_Core_Model_File_UploaderFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_uploaderFactoryMock;

    /**
     * @var Magento_Backend_Model_Session|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionMock;

    /**
     * @var Magento_Backend_Model_Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_backendUrlMock;

    protected function setUp()
    {
        $this->_filesystemMock = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $this->_adapterFactoryMock = $this->getMock(
            'Magento_Core_Model_Image_AdapterFactory', array(), array(), '', false
        );
        $this->_viewUrlMock = $this->getMock('Magento_Core_Model_View_Url', array(), array(), '', false);
        $this->_imageHelperMock = $this->getMock('Magento_Cms_Helper_Wysiwyg_Images', array(), array(), '', false);
        $this->_resizeParameters = array('width' => 100, 'height' => 50);

        $this->_dirMock = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false);
        $this->_storageCollectionFactoryMock = $this->getMock(
            'Magento_Cms_Model_Wysiwyg_Images_Storage_CollectionFactory');
        $this->_storageFileFactoryMock = $this->getMock('Magento_Core_Model_File_Storage_FileFactory');
        $this->_storageDatabaseFactoryMock = $this->getMock('Magento_Core_Model_File_Storage_DatabaseFactory');
        $this->_directoryDatabaseFactoryMock = $this->getMock(
            'Magento_Core_Model_File_Storage_Directory_DatabaseFactory');
        $this->_uploaderFactoryMock = $this->getMock('Magento_Core_Model_File_UploaderFactory');
        $this->_sessionMock = $this->getMock('Magento_Backend_Model_Session', array(), array(), '', false);
        $this->_backendUrlMock = $this->getMock('Magento_Backend_Model_Url', array(), array(), '', false);

        $this->_imageHelperMock->expects($this->once())
            ->method('getStorageRoot')
            ->will($this->returnValue('someDirectory'));

        $this->_filesystemMock->expects($this->once())
            ->method('setWorkingDirectory')
            ->with('someDirectory');

        $this->_filesystemMock->expects($this->once())
            ->method('setIsAllowCreateDirectories')
            ->with(true);

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento_Cms_Model_Wysiwyg_Images_Storage', array(
            'session' => $this->_sessionMock,
            'backendUrl' => $this->_backendUrlMock,
            'cmsWysiwygImages' => $this->_imageHelperMock,
            'coreFileStorageDb' => $this->getMock('Magento_Core_Helper_File_Storage_Database', array(), array(), '',
                false),
            'filesystem' => $this->_filesystemMock,
            'imageFactory' => $this->_adapterFactoryMock,
            'viewUrl' => $this->_viewUrlMock,
            'dir' => $this->_dirMock,
            'storageCollectionFactory' => $this->_storageCollectionFactoryMock,
            'storageFileFactory' => $this->_storageFileFactoryMock,
            'storageDatabaseFactory' => $this->_storageDatabaseFactoryMock,
            'directoryDatabaseFactory' => $this->_directoryDatabaseFactoryMock,
            'uploaderFactory' => $this->_uploaderFactoryMock,
            'resizeParameters' => $this->_resizeParameters,
        ));
    }

    /**
     * @covers Magento_Cms_Model_Wysiwyg_Images_Storage::getResizeWidth
     */
    public function testGetResizeWidth()
    {
        $this->assertEquals(100, $this->_model->getResizeWidth());
    }

    /**
     * @covers Magento_Cms_Model_Wysiwyg_Images_Storage::getResizeHeight
     */
    public function testGetResizeHeight()
    {
        $this->assertEquals(50, $this->_model->getResizeHeight());
    }
}
