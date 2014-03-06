<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test of image path model
 */
namespace Magento\Core\Model\Theme\Image;

class PathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Theme\Image\Path|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $viewFilesystem;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\View\Url
     */
    protected $viewUrlMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\StoreManager
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Filesystem\Directory\ReadInterface
     */
    protected $mediaDirectory;

    protected function setUp()
    {
        $this->filesystem = $this->getMock('Magento\App\Filesystem', array(), array(), '', false);
        $this->mediaDirectory = $this->getMock(
            'Magento\Filesystem\Directory\ReadInterface', array(), array(), '', false
        );
        $this->viewFilesystem = $this->getMock('Magento\View\FileSystem', array(), array(), '', false);
        $this->viewUrlMock = $this->getMock('Magento\View\Url', array(), array(), '', false);
        $this->storeManagerMock = $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false);


        $this->mediaDirectory->expects($this->any())
            ->method('getAbsolutePath')
            ->with(\Magento\View\Design\Theme\Image\PathInterface::PREVIEW_DIRECTORY_PATH)
            ->will($this->returnValue('/theme/preview'));

        $this->mediaDirectory->expects($this->any())
            ->method('getRelativePath')
            ->with('/theme/origin')
            ->will($this->returnValue('/theme/origin'));

        $this->filesystem->expects($this->any())->method('getDirectoryRead')->with(\Magento\App\Filesystem::MEDIA_DIR)
            ->will($this->returnValue($this->mediaDirectory));

        $this->model = new Path(
            $this->filesystem,
            $this->viewFilesystem,
            $this->viewUrlMock,
            $this->storeManagerMock
        );
    }

    protected function tearDown()
    {
        $this->model = null;
        $this->filesystem = null;
        $this->viewUrlMock = null;
        $this->storeManagerMock = null;
    }

    public function testGetPreviewImageUrlPhysicalTheme()
    {
        $theme = $this->getGetTheme(true);

        $this->viewUrlMock->expects($this->any())
            ->method('getViewFileUrl')
            ->with($theme->getPreviewImage(), ['area' => $theme->getData('area'), 'themeModel' => $theme])
            ->will($this->returnValue('http://localhost/theme/preview/image.png'));

        $this->assertEquals('http://localhost/theme/preview/image.png', $this->model->getPreviewImageUrl($theme));
    }

    public function testGetPreviewImageUrlVirtualTheme()
    {
        $theme = $this->getGetTheme(false);

        $store = $this->getMock('Magento\Core\Model\Store', array(), array(), '', false);
        $store->expects($this->any())->method('getBaseUrl')->will($this->returnValue('http://localhost/'));
        $this->storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($store));
        $this->assertEquals('http://localhost/theme/preview/image.png', $this->model->getPreviewImageUrl($theme));
    }

    protected  function getGetTheme($isPhysical)
    {
        /** @var $theme \Magento\Core\Model\Theme|\PHPUnit_Framework_MockObject_MockObject */
        $theme = $this->getMock(
            'Magento\Core\Model\Theme',
            array('getPreviewImage', 'isPhysical','__wakeup'),
            array(),
            '',
            false
        );

        $theme->setData('area', 'frontend');

        $theme->expects($this->any())
            ->method('isPhysical')
            ->will($this->returnValue($isPhysical));

        $theme->expects($this->any())
            ->method('getPreviewImage')
            ->will($this->returnValue('image.png'));

        return $theme;
    }

    /**
     * @covers Magento\Core\Model\Theme\Image\Path::getPreviewImageDefaultUrl
     */
    public function testDefaultPreviewImageUrlGetter()
    {
        $this->viewUrlMock->expects($this->once())->method('getViewFileUrl')
            ->with(\Magento\Core\Model\Theme\Image\Path::DEFAULT_PREVIEW_IMAGE);
        $this->model->getPreviewImageDefaultUrl();
    }

    /**
     * @covers \Magento\Core\Model\Theme\Image\Path::getImagePreviewDirectory
     */
    public function testImagePreviewDirectoryGetter()
    {
        $this->assertEquals(
            '/theme/preview',
            $this->model->getImagePreviewDirectory()
        );
    }

    /**
     * @covers \Magento\Core\Model\Theme\Image\Path::getTemporaryDirectory
     */
    public function testTemporaryDirectoryGetter()
    {
        $this->assertEquals(
            '/theme/origin',
            $this->model->getTemporaryDirectory()
        );
    }
}
