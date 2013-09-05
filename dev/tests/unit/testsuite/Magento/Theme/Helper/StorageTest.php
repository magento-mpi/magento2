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
 * Storage helper test
 */
class Magento_Theme_Helper_StorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var null|\Magento\Filesystem|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var Magento_Backend_Model_Session|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_session;

    /**
     * @var Magento_Core_Model_Theme_FlyweightFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_themeFactory;

    /**
     * @var Zend_Controller_Request_Http|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var Magento_Theme_Helper_Storage
     */
    protected $_storageHelper;

    /**
     * @var string
     */
    protected $_customizationPath;

    public function setUp()
    {
        $this->_customizationPath = \Magento\Filesystem::DIRECTORY_SEPARATOR
            . implode(\Magento\Filesystem::DIRECTORY_SEPARATOR, array('var', 'theme'));

        $this->_request = $this->getMock('Zend_Controller_Request_Http', array('getParam'), array(), '', false);
        $this->_filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_session = $this->getMock('Magento_Backend_Model_Session', array(), array(), '', false);
        $this->_themeFactory = $this->getMock('Magento_Core_Model_Theme_FlyweightFactory', array('create'), array(),
            '', false);

        $this->_storageHelper = $this->getMock('Magento_Theme_Helper_Storage',
            array('_getRequest', 'urlDecode'), array(), '', false
        );
        $this->_storageHelper->expects($this->any())
            ->method('_getRequest')
            ->will($this->returnValue($this->_request));
        $this->_storageHelper->expects($this->any())
            ->method('urlDecode')
            ->will($this->returnArgument(0));

        $filesystemProperty = new ReflectionProperty($this->_storageHelper, '_filesystem');
        $filesystemProperty->setAccessible(true);
        $filesystemProperty->setValue($this->_storageHelper, $this->_filesystem);

        $sessionProperty = new ReflectionProperty($this->_storageHelper, '_session');
        $sessionProperty->setAccessible(true);
        $sessionProperty->setValue($this->_storageHelper, $this->_session);

        $themeFactoryProperty = new ReflectionProperty($this->_storageHelper, '_themeFactory');
        $themeFactoryProperty->setAccessible(true);
        $themeFactoryProperty->setValue($this->_storageHelper, $this->_themeFactory);
    }

    public function tearDown()
    {
        $this->_filesystem = null;
        $this->_session = null;
        $this->_themeFactory = null;
        $this->_request = null;
        $this->_storageHelper = null;
        $this->_customizationPath = null;
    }

    /**
     * @param $path
     */
    protected function _mockStorageRoot($path)
    {
        $storageRootProperty = new ReflectionProperty($this->_storageHelper, '_storageRoot');
        $storageRootProperty->setAccessible(true);
        $storageRootProperty->setValue($this->_storageHelper, $path);
    }

    /**
     * @param $path
     */
    protected function _mockCurrentPath($path)
    {
        $currentPathProperty = new ReflectionProperty($this->_storageHelper, '_currentPath');
        $currentPathProperty->setAccessible(true);
        $currentPathProperty->setValue($this->_storageHelper, $path);
    }

    /**
     * @covers Magento_Theme_Helper_Storage::getShortFilename
     */
    public function testGetShortFilename()
    {
        $longFileName     = 'veryLongFileNameMoreThanTwenty';
        $expectedFileName = 'veryLongFileNameMore...';
        $this->assertEquals($expectedFileName, $this->_storageHelper->getShortFilename($longFileName, 20));
    }

    /**
     * @covers Magento_Theme_Helper_Storage::getStorageRoot
     * @covers Magento_Theme_Helper_Storage::_getTheme
     * @covers Magento_Theme_Helper_Storage::getStorageType
     */
    public function testGetStorageRoot()
    {
        $themeId = 6;
        $requestMap = array(
            array(Magento_Theme_Helper_Storage::PARAM_THEME_ID, null, $themeId),
            array(
                Magento_Theme_Helper_Storage::PARAM_CONTENT_TYPE,
                null,
                Magento_Theme_Model_Wysiwyg_Storage::TYPE_IMAGE
            )
        );
        $this->_request->expects($this->any())
            ->method('getParam')
            ->will($this->returnValueMap($requestMap));

        $themeModel = $this->getMock('Magento_Core_Model_Theme', array(), array(), '', false);
        $this->_themeFactory->expects($this->any())->method('create')->will($this->returnValue($themeModel));
        $themeModel->expects($this->any())->method('getId')->will($this->returnValue($themeId));
        $customization = $this->getMock('Magento_Core_Model_Theme_Customization', array(), array(), '', false);
        $themeModel->expects($this->any())->method('getCustomization')->will($this->returnValue($customization));
        $customization->expects($this->any())
            ->method('getCustomizationPath')
            ->will($this->returnValue($this->_customizationPath));

        $expectedStorageRoot = implode(\Magento\Filesystem::DIRECTORY_SEPARATOR, array(
            $this->_customizationPath,
            Magento_Theme_Model_Wysiwyg_Storage::TYPE_IMAGE
        ));
        $this->assertEquals($expectedStorageRoot, $this->_storageHelper->getStorageRoot());
    }

    /**
     * @covers Magento_Theme_Helper_Storage::getThumbnailDirectory
     */
    public function testGetThumbnailDirectory()
    {
        $imagePath = implode(\Magento\Filesystem::DIRECTORY_SEPARATOR, array('root', 'image', 'image_name.jpg'));
        $thumbnailDir = implode(
            \Magento\Filesystem::DIRECTORY_SEPARATOR,
            array('root', 'image', Magento_Theme_Model_Wysiwyg_Storage::THUMBNAIL_DIRECTORY)
        );

        $this->assertEquals($thumbnailDir, $this->_storageHelper->getThumbnailDirectory($imagePath));
    }

    /**
     * @covers Magento_Theme_Helper_Storage::getThumbnailPath
     */
    public function testGetThumbnailPath()
    {
        $image       = 'image_name.jpg';
        $storageRoot = $this->_customizationPath . \Magento\Filesystem::DIRECTORY_SEPARATOR
            . Magento_Theme_Model_Wysiwyg_Storage::TYPE_IMAGE;
        $currentPath = $storageRoot . \Magento\Filesystem::DIRECTORY_SEPARATOR . 'some_dir';

        $imagePath   = $currentPath . \Magento\Filesystem::DIRECTORY_SEPARATOR . $image;
        $thumbnailPath = implode(
            \Magento\Filesystem::DIRECTORY_SEPARATOR,
            array($currentPath, Magento_Theme_Model_Wysiwyg_Storage::THUMBNAIL_DIRECTORY, $image)
        );

        $this->_filesystem->expects($this->atLeastOnce())
            ->method('has')
            ->with($imagePath)
            ->will($this->returnValue(true));

        $this->_filesystem->expects($this->atLeastOnce())
            ->method('isPathInDirectory')
            ->with($imagePath, $storageRoot)
            ->will($this->returnValue(true));

        $this->_mockStorageRoot($storageRoot);
        $this->_mockCurrentPath($currentPath);

        $this->assertEquals($thumbnailPath, $this->_storageHelper->getThumbnailPath($image));
    }

    /**
     * @covers Magento_Theme_Helper_Storage::getRequestParams
     */
    public function testGetRequestParams()
    {
        $node = 'node';
        $themeId = 16;
        $contentType = Magento_Theme_Model_Wysiwyg_Storage::TYPE_IMAGE;

        $requestMap = array(
            array(Magento_Theme_Helper_Storage::PARAM_NODE, null, $node),
            array(Magento_Theme_Helper_Storage::PARAM_THEME_ID, null, $themeId),
            array(Magento_Theme_Helper_Storage::PARAM_CONTENT_TYPE, null, $contentType)
        );
        $this->_request->expects($this->any())
            ->method('getParam')
            ->will($this->returnValueMap($requestMap));

        $expectedResult = array(
            Magento_Theme_Helper_Storage::PARAM_THEME_ID     => $themeId,
            Magento_Theme_Helper_Storage::PARAM_CONTENT_TYPE => $contentType,
            Magento_Theme_Helper_Storage::PARAM_NODE         => $node
        );
        $this->assertEquals($expectedResult, $this->_storageHelper->getRequestParams());
    }

    /**
     * @covers Magento_Theme_Helper_Storage::getAllowedExtensionsByType
     */
    public function testGetAllowedExtensionsByType()
    {
        $this->_request->expects($this->at(0))
            ->method('getParam')
            ->with(Magento_Theme_Helper_Storage::PARAM_CONTENT_TYPE)
            ->will($this->returnValue(Magento_Theme_Model_Wysiwyg_Storage::TYPE_FONT));

        $this->_request->expects($this->at(1))
            ->method('getParam')
            ->with(Magento_Theme_Helper_Storage::PARAM_CONTENT_TYPE)
            ->will($this->returnValue(Magento_Theme_Model_Wysiwyg_Storage::TYPE_IMAGE));


        $fontTypes = $this->_storageHelper->getAllowedExtensionsByType();
        $this->assertEquals(array('ttf', 'otf', 'eot', 'svg', 'woff'), $fontTypes);

        $imagesTypes = $this->_storageHelper->getAllowedExtensionsByType();
        $this->assertEquals(array('jpg', 'jpeg', 'gif', 'png', 'xbm', 'wbmp'), $imagesTypes);
    }
}
