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
     * @var \Magento\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $session;

    /**
     * @var \Magento\View\Design\Theme\FlyweightFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $themeFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Theme\Helper\Storage
     */
    protected $helper;

    /**
     * @var string
     */
    protected $customizationPath;

    /**
     * @var \Magento\Filesystem\Directory\Write|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryWrite;

    /**
     * @var \Magento\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
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

    protected $requestParams;

    protected function setUp()
    {
        $this->customizationPath = '/' . implode('/', array('var', 'theme'));

        $this->request          = $this->getMock('\Magento\App\Request\Http', array(), array(), '', false);
        $this->filesystem       = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->session          = $this->getMock('Magento\Backend\Model\Session', array(), array(), '', false);
        $this->contextHelper    = $this->getMock('Magento\App\Helper\Context', array(), array(), '', false);
        $this->directoryWrite   = $this->getMock('Magento\Filesystem\Directory\Write', array(), array(), '', false);
        $this->themeFactory     = $this->getMock(
            'Magento\View\Design\Theme\FlyweightFactory',
            array(),
            array(),
            '',
            false
        );
        $this->theme            = $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false);
        $this->customization    = $this->getMock(
            'Magento\View\Design\Theme\Customization',
            array(),
            array(),
            '',
            false
        );

        $this->filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->will($this->returnValue($this->directoryWrite));

        $this->directoryWrite->expects($this->any())
            ->method('create')
            ->will($this->returnValue(true));

        $this->contextHelper->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));

        $this->themeFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->theme));

        $this->theme->expects($this->any())
            ->method('getCustomization')
            ->will($this->returnValue($this->customization));

        $this->request->expects($this->at(0))
            ->method('getParam')
            ->with(\Magento\Theme\Helper\Storage::PARAM_THEME_ID)
            ->will($this->returnValue(6));
        $this->request->expects($this->at(1))
            ->method('getParam')
            ->with(\Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE)
            ->will($this->returnValue(\Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE));

        $this->helper = new \Magento\Theme\Helper\Storage(
            $this->contextHelper,
            $this->filesystem,
            $this->session,
            $this->themeFactory
        );
    }

    protected function tearDown()
    {
        $this->request          = null;
        $this->filesystem       = null;
        $this->session          = null;
        $this->contextHelper    = null;
        $this->directoryWrite   = null;
        $this->themeFactory     = null;
        $this->theme            = null;
        $this->customization    = null;
    }

    /**
     * @covers \Magento\Theme\Helper\Storage::getShortFilename
     */
    public function testGetShortFilename()
    {
        $longFileName     = 'veryLongFileNameMoreThanTwenty';
        $expectedFileName = 'veryLongFileNameMore...';
        $this->assertEquals($expectedFileName, $this->helper->getShortFilename($longFileName, 20));
    }

    public function testGetStorageRoot()
    {
        $expectedStorageRoot = '/' . \Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE;
        $this->assertEquals($expectedStorageRoot, $this->helper->getStorageRoot());
    }

    public function testGetThumbnailDirectory()
    {
        $imagePath = implode('/', array('root', 'image', 'image_name.jpg'));
        $thumbnailDir = implode(
            '/',
            array('root', 'image', \Magento\Theme\Model\Wysiwyg\Storage::THUMBNAIL_DIRECTORY)
        );

        $this->assertEquals($thumbnailDir, $this->helper->getThumbnailDirectory($imagePath));
    }

    public function testGetThumbnailPath()
    {
        $image       = 'image_name.jpg';
        $thumbnailPath = '/' . implode('/', array(
            \Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE,
            \Magento\Theme\Model\Wysiwyg\Storage::THUMBNAIL_DIRECTORY, $image)
        );

        $this->customization->expects($this->any())
            ->method('getCustomizationPath')
            ->will($this->returnValue($this->customizationPath));

        $this->directoryWrite->expects($this->any())
            ->method('isExist')
            ->will($this->returnValue(true));

        $this->assertEquals($thumbnailPath, $this->helper->getThumbnailPath($image));
    }

    public function testGetRequestParams()
    {
        $this->request->expects($this->at(0))
            ->method('getParam')
            ->with(\Magento\Theme\Helper\Storage::PARAM_THEME_ID)
            ->will($this->returnValue(6));
        $this->request->expects($this->at(1))
            ->method('getParam')
            ->with(\Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE)
            ->will($this->returnValue('image'));
        $this->request->expects($this->at(2))
            ->method('getParam')
            ->with(\Magento\Theme\Helper\Storage::PARAM_NODE)
            ->will($this->returnValue('node'));

        $expectedResult = array(
            \Magento\Theme\Helper\Storage::PARAM_THEME_ID     => 6,
            \Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE => \Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE,
            \Magento\Theme\Helper\Storage::PARAM_NODE         => 'node'
        );
        $this->assertEquals($expectedResult, $this->helper->getRequestParams());
    }

    public function testGetAllowedExtensionsByType()
    {
        $this->request->expects($this->at(0))
            ->method('getParam')
            ->with(\Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE)
            ->will($this->returnValue(\Magento\Theme\Model\Wysiwyg\Storage::TYPE_FONT));

        $this->request->expects($this->at(1))
            ->method('getParam')
            ->with(\Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE)
            ->will($this->returnValue(\Magento\Theme\Model\Wysiwyg\Storage::TYPE_IMAGE));


        $fontTypes = $this->helper->getAllowedExtensionsByType();
        $this->assertEquals(array('ttf', 'otf', 'eot', 'svg', 'woff'), $fontTypes);

        $imagesTypes = $this->helper->getAllowedExtensionsByType();
        $this->assertEquals(array('jpg', 'jpeg', 'gif', 'png', 'xbm', 'wbmp'), $imagesTypes);
    }
}
