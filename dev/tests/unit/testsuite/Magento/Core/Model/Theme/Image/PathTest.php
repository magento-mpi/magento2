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
     * @var \Magento\View\Design\Theme\Image\Path|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewUrlMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_url;

    protected function setUp()
    {
        $this->_dirMock = $this->getMock('Magento\App\Dir', array(), array(), '', false);
        $this->_viewUrlMock = $this->getMock('Magento\View\Url', array(), array(), '', false);
        $this->_url = $this->getMock('Magento\UrlInterface', array(), array(), '', false);

        $this->_dirMock->expects($this->any())->method('getDir')->with(\Magento\App\Dir::MEDIA)
            ->will($this->returnValue('/media'));

        $this->_model = new \Magento\View\Design\Theme\Image\Path(
            $this->_dirMock,
            $this->_viewUrlMock,
            $this->_url
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_dirMock = null;
        $this->_viewUrlMock = null;
        $this->_url = null;
    }

    /**
     * @covers \Magento\Core\Model\Theme\Image\Path::__construct
     * @covers \Magento\Core\Model\Theme\Image\Path::getPreviewImageDirectoryUrl
     */
    public function testPreviewImageDirectoryUrlGetter()
    {
        $this->_url->expects($this->any())->method('getBaseUrl')->will($this->returnValue('http://localhost/'));
        $this->assertEquals('http://localhost/theme/preview/', $this->_model->getPreviewImageDirectoryUrl());
    }

    /**
     * @covers \Magento\Core\Model\Theme\Image\Path::getPreviewImageDefaultUrl
     */
    public function testDefaultPreviewImageUrlGetter()
    {
        $this->_viewUrlMock->expects($this->once())->method('getViewFileUrl')
            ->with(\Magento\View\Design\Theme\Image\Path::DEFAULT_PREVIEW_IMAGE);
        $this->_model->getPreviewImageDefaultUrl();
    }

    /**
     * @covers \Magento\Core\Model\Theme\Image\Path::getImagePreviewDirectory
     */
    public function testImagePreviewDirectoryGetter()
    {
        $this->assertEquals(
            \Magento\Filesystem::fixSeparator('/media/theme/preview'),
            \Magento\Filesystem::fixSeparator($this->_model->getImagePreviewDirectory())
        );
    }

    /**
     * @covers \Magento\Core\Model\Theme\Image\Path::getTemporaryDirectory
     */
    public function testTemporaryDirectoryGetter()
    {
        $this->assertEquals(
            \Magento\Filesystem::fixSeparator('/media/theme/origin'),
            \Magento\Filesystem::fixSeparator($this->_model->getTemporaryDirectory())
        );
    }
}
