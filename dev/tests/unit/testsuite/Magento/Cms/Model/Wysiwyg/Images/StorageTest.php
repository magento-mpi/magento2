<?php

namespace Magento\Cms\Model\Wysiwyg\Images;

/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Images\Storage
     */
    protected $_model = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewUrlMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapterFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_imageHelperMock;

    /**
     * @var array()
     */
    protected $_resizeParameters;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Images\Storage\CollectionFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageCollectionFactoryMock;

    /**
     * @var \Magento\Core\Model\File\Storage\FileFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageFileFactoryMock;

    /**
     * @var \Magento\Core\Model\File\Storage\DatabaseFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageDatabaseFactoryMock;

    /**
     * @var \Magento\Core\Model\File\Storage\Directory\DatabaseFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_directoryDatabaseFactoryMock;

    /**
     * @var \Magento\Core\Model\File\UploaderFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_uploaderFactoryMock;

    /**
     * @var \Magento\Backend\Model\Session|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionMock;

    /**
     * @var \Magento\Backend\Model\Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_backendUrlMock;

    protected function setUp()
    {
        $this->_filesystemMock = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_adapterFactoryMock = $this->getMock(
            'Magento\Image\AdapterFactory', array(), array(), '', false
        );
        $this->_viewUrlMock = $this->getMock('Magento\View\Url', array(), array(), '', false);
        $this->_imageHelperMock = $this->getMock('Magento\Cms\Helper\Wysiwyg\Images', array(), array(), '', false);
        $this->_resizeParameters = array('width' => 100, 'height' => 50);

        $this->_storageCollectionFactoryMock = $this->getMock(
            'Magento\Cms\Model\Wysiwyg\Images\Storage\CollectionFactory');
        $this->_storageFileFactoryMock = $this->getMock('Magento\Core\Model\File\Storage\FileFactory');
        $this->_storageDatabaseFactoryMock = $this->getMock('Magento\Core\Model\File\Storage\DatabaseFactory');
        $this->_directoryDatabaseFactoryMock = $this->getMock(
            'Magento\Core\Model\File\Storage\Directory\DatabaseFactory');
        $this->_uploaderFactoryMock = $this->getMock('Magento\Core\Model\File\UploaderFactory');
        $this->_sessionMock = $this->getMock('Magento\Backend\Model\Session', array(), array(), '', false);
        $this->_backendUrlMock = $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento\Cms\Model\Wysiwyg\Images\Storage', array(
            'session' => $this->_sessionMock,
            'backendUrl' => $this->_backendUrlMock,
            'cmsWysiwygImages' => $this->_imageHelperMock,
            'coreFileStorageDb' => $this->getMock('Magento\Core\Helper\File\Storage\Database', array(), array(), '',
                false),
            'filesystem' => $this->_filesystemMock,
            'imageFactory' => $this->_adapterFactoryMock,
            'viewUrl' => $this->_viewUrlMock,
            'storageCollectionFactory' => $this->_storageCollectionFactoryMock,
            'storageFileFactory' => $this->_storageFileFactoryMock,
            'storageDatabaseFactory' => $this->_storageDatabaseFactoryMock,
            'directoryDatabaseFactory' => $this->_directoryDatabaseFactoryMock,
            'uploaderFactory' => $this->_uploaderFactoryMock,
            'resizeParameters' => $this->_resizeParameters,
        ));
    }

    /**
     * @covers \Magento\Cms\Model\Wysiwyg\Images\Storage::getResizeWidth
     */
    public function testGetResizeWidth()
    {
        $this->assertEquals(100, $this->_model->getResizeWidth());
    }

    /**
     * @covers \Magento\Cms\Model\Wysiwyg\Images\Storage::getResizeHeight
     */
    public function testGetResizeHeight()
    {
        $this->assertEquals(50, $this->_model->getResizeHeight());
    }
}
