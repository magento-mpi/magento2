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
namespace Magento\Theme\Helper;

class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var null|\Magento\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var \Magento\Backend\Model\Session|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_session;

    /**
     * @var \Magento\View\Design\Theme\FlyweightFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_themeFactory;

    /**
     * @var \Magento\View\Design\Theme\FlyweightFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $themeFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var \Magento\Theme\Helper\Storage
     */
    protected $_storageHelper;

    /**
     * @var \Magento\Theme\Helper\Storage
     */
    protected $helper;

    /**
     * @var string
     */
    protected $_customizationPath;

    /**
     * @var \Magento\Filesystem\Directory\Read|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryRead;

    /**
     * @var \Magento\Core\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextHelper;

    /**
     * @var \Magento\Core\Model\Theme|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $theme;

    /**
     * @var \Magento\View\Design\Theme\Customization|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customization;


    protected function setUp()
    {
        $this->_customizationPath = \Magento\Filesystem::DIRECTORY_SEPARATOR
            . implode(\Magento\Filesystem::DIRECTORY_SEPARATOR, array('var', 'theme'));

        $this->_request = $this->getMock('\Magento\App\Request\Http', array(), array(), '', false);
        $this->_filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_session = $this->getMock('Magento\Backend\Model\Session', array(), array(), '', false);
        $this->_themeFactory = $this->getMock('Magento\View\Design\Theme\FlyweightFactory', array('create'), array(),
            '', false);


        $this->contextHelper    = $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false);
        $this->directoryRead    = $this->getMock('Magento\Filesystem\Directory\Read', array(), array(), '', false);
        $this->themeFactory     = $this->getMock('Magento\View\Design\Theme\FlyweightFactory',
            array(), array(), '', false);
        $this->theme            = $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false);
        $this->customization    = $this->getMock('Magento\View\Design\Theme\Customization',
            array(), array(), '', false);

        $this->_filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->will($this->returnValue($this->directoryRead));
        $this->contextHelper->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->_request));
        $this->themeFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->theme));
        $this->theme->expects($this->any())
            ->method('getCustomization')
            ->will($this->returnValue($this->customization));

        $this->helper = new \Magento\Theme\Helper\Storage(
            $this->_filesystem, $this->_session, $this->themeFactory, $this->contextHelper);

        $this->_storageHelper = $this->getMock('Magento\Theme\Helper\Storage',
            array('_getRequest', 'urlDecode'), array(), '', false
        );
        $this->_storageHelper->expects($this->any())
            ->method('_getRequest')
            ->will($this->returnValue($this->_request));
        $this->_storageHelper->expects($this->any())
            ->method('urlDecode')
            ->will($this->returnArgument(0));

        $filesystemProperty = new \ReflectionProperty($this->_storageHelper, 'filesystem');
        $filesystemProperty->setAccessible(true);
        $filesystemProperty->setValue($this->_storageHelper, $this->_filesystem);

        $sessionProperty = new \ReflectionProperty($this->_storageHelper, '_session');
        $sessionProperty->setAccessible(true);
        $sessionProperty->setValue($this->_storageHelper, $this->_session);

        $themeFactoryProperty = new \ReflectionProperty($this->_storageHelper, '_themeFactory');
        $themeFactoryProperty->setAccessible(true);
        $themeFactoryProperty->setValue($this->_storageHelper, $this->_themeFactory);
    }

    protected function tearDown()
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
        $storageRootProperty = new \ReflectionProperty($this->_storageHelper, '_storageRoot');
        $storageRootProperty->setAccessible(true);
        $storageRootProperty->setValue($this->_storageHelper, $path);
    }

    /**
     * @param $path
     */
    protected function _mockCurrentPath($path)
    {
        $currentPathProperty = new \ReflectionProperty($this->_storageHelper, '_currentPath');
        $currentPathProperty->setAccessible(true);
        $currentPathProperty->setValue($this->_storageHelper, $path);
    }

    /**
     * @covers \Magento\Theme\Helper\Storage::getShortFilename
     */
    public function testGetShortFilename()
    {
        $longFileName     = 'veryLongFileNameMoreThanTwenty';
        $expectedFileName = 'veryLongFileNameMore...';
        $this->assertEquals($expectedFileName, $this->_storageHelper->getShortFilename($longFileName, 20));
    }

    /**
     * @covers \Magento\Theme\Helper\Storage::getStorageRoot
     * @covers \Magento\Theme\Helper\Storage::_getTheme
     * @covers \Magento\Theme\Helper\Storage::getStorageType
     */
    public function testGetStorageRoot()
    {
        $themeId = 6;
        $requestMap = array(
            array(\Magento\Theme\Helper\Storage::PARAM_THEME_ID, null, $themeId),
            array(
                \Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE,
                null,
                \Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE
            )
        );
        $this->_request->expects($this->any())
            ->method('getParam')
            ->will($this->returnValueMap($requestMap));

        $themeModel = $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false);
        $this->_themeFactory->expects($this->any())->method('create')->will($this->returnValue($themeModel));
        $themeModel->expects($this->any())->method('getId')->will($this->returnValue($themeId));
        $customization = $this->getMock('Magento\View\Design\Theme\Customization', array(), array(), '', false);
        $themeModel->expects($this->any())->method('getCustomization')->will($this->returnValue($customization));
        $customization->expects($this->any())
            ->method('getCustomizationPath')
            ->will($this->returnValue($this->_customizationPath));

        $expectedStorageRoot = implode(\Magento\Filesystem::DIRECTORY_SEPARATOR, array(
            $this->_customizationPath,
            \Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE
        ));
        $this->assertEquals($expectedStorageRoot, $this->_storageHelper->getStorageRoot());
    }

    /**
     * @covers \Magento\Theme\Helper\Storage::getThumbnailDirectory
     */
    public function testGetThumbnailDirectory()
    {
        $imagePath = implode(\Magento\Filesystem::DIRECTORY_SEPARATOR, array('root', 'image', 'image_name.jpg'));
        $thumbnailDir = implode(
            \Magento\Filesystem::DIRECTORY_SEPARATOR,
            array('root', 'image', \Magento\Theme\Model\Wysiwyg\Storage::THUMBNAIL_DIRECTORY)
        );

        $this->assertEquals($thumbnailDir, $this->_storageHelper->getThumbnailDirectory($imagePath));
    }

    /**
     * @covers \Magento\Theme\Helper\Storage::getThumbnailPath
     */
    public function testGetThumbnailPath()
    {
        $image       = 'image_name.jpg';
        $storageRoot = $this->_customizationPath . '/'
            . \Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE;
        $thumbnailPath = implode(
            '/',
            array($storageRoot, \Magento\Theme\Model\Wysiwyg\Storage::THUMBNAIL_DIRECTORY, $image)
        );

        $this->customization->expects($this->any())
            ->method('getCustomizationPath')
            ->will($this->returnValue($this->_customizationPath));

        $this->_request->expects($this->at(0))
            ->method('getParam')
            ->with($this->equalTo(\Magento\Theme\Helper\Storage::PARAM_THEME_ID))
            ->will($this->returnValue(100500));

        $this->_request->expects($this->at(1))
            ->method('getParam')
            ->with($this->equalTo(\Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE))
            ->will($this->returnValue(\Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE));

        $this->directoryRead->expects($this->any())
            ->method('isExist')
//            ->with($imagePath)
            ->will($this->returnValue(true));

        $this->assertEquals($thumbnailPath, $this->helper->getThumbnailPath($image));
    }

    /**
     * @covers \Magento\Theme\Helper\Storage::getRequestParams
     */
    public function testGetRequestParams()
    {
        $node = 'node';
        $themeId = 16;
        $contentType = \Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE;

        $requestMap = array(
            array(\Magento\Theme\Helper\Storage::PARAM_NODE, null, $node),
            array(\Magento\Theme\Helper\Storage::PARAM_THEME_ID, null, $themeId),
            array(\Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE, null, $contentType)
        );
        $this->_request->expects($this->any())
            ->method('getParam')
            ->will($this->returnValueMap($requestMap));


        $expectedResult = array(
            \Magento\Theme\Helper\Storage::PARAM_THEME_ID     => $themeId,
            \Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE => $contentType,
            \Magento\Theme\Helper\Storage::PARAM_NODE         => $node
        );
        $this->assertEquals($expectedResult, $this->_storageHelper->getRequestParams());
    }

    /**
     * @covers \Magento\Theme\Helper\Storage::getAllowedExtensionsByType
     */
    public function testGetAllowedExtensionsByType()
    {
        $this->_request->expects($this->at(0))
            ->method('getParam')
            ->with(\Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE)
            ->will($this->returnValue(\Magento\Theme\Model\Wysiwyg\Storage::TYPE_FONT));

        $this->_request->expects($this->at(1))
            ->method('getParam')
            ->with(\Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE)
            ->will($this->returnValue(\Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE));


        $fontTypes = $this->_storageHelper->getAllowedExtensionsByType();
        $this->assertEquals(array('ttf', 'otf', 'eot', 'svg', 'woff'), $fontTypes);

        $imagesTypes = $this->_storageHelper->getAllowedExtensionsByType();
        $this->assertEquals(array('jpg', 'jpeg', 'gif', 'png', 'xbm', 'wbmp'), $imagesTypes);
    }
}
