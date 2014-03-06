<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

class MinifiedTest extends \PHPUnit_Framework_TestCase
{
    const ORIG_FILE = '/home/htdocs/some/dir/original.js';
    const ORIG_PREMINIFIED_FILE = '/home/htdocs/some/dir/original.min.js';
    const MINIFIED_FILE = '/home/htdocs/static_dir/minified/original.min.js';

    const STATIC_BASE_URL = 'http://localhost/static_dir/';

    const ORIG_URL = 'http://localhost/static_dir/some/other/subdir/original.js';
    const ORIG_PREMINIFIED_URL = 'http://localhost/static_dir/some/other/subdir/original.min.js';
    const MINIFIED_URL = 'http://localhost/static_dir/minified/original.min.js';

    const ORIG_RELPATH = 'some/other/subdir/original.js';
    const ORIG_PREMINIFIED_RELPATH = 'some/other/subdir/original.min.js';
    const MINIFIED_RELPATH = 'minified/original.min.js';

    /**
     * @var \Magento\View\Asset\LocalInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_asset;

    /**
     * @var \Magento\Code\Minifier|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_minifier;

    /**
     * @var \Magento\Logger|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logger;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_staticViewDir;

    /**
     * @var \Magento\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_baseUrl;

    /**
     * @var \Magento\View\Asset\Minified
     */
    protected $_model;

    protected function setUp()
    {
        $this->_asset = $this->getMockForAbstractClass(
            '\Magento\View\Asset\LocalInterface',
            array(),
            '',
            false
        );
        $this->_minifier = $this->getMock('\Magento\Code\Minifier', array('getMinifiedFile'), array(), '', false);
        $this->_logger = $this->getMock('\Magento\Logger', array(), array(), '', false);
        $this->_baseUrl = $this->getMock('\Magento\Url', array(), array(), '', false);
        $this->_staticViewDir = $this->getMock('\Magento\Filesystem\Directory\ReadInterface', array(), array(), '',
            false);

        $filesystem = $this->getMock('\Magento\App\Filesystem', array(), array(), '', false);
        $filesystem->expects($this->any())
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::STATIC_VIEW_DIR)
            ->will($this->returnValue($this->_staticViewDir));

        $this->_model = new \Magento\View\Asset\Minified($this->_asset, $this->_minifier, $this->_logger,
            $filesystem, $this->_baseUrl);
    }

    protected function tearDown()
    {
        $this->_asset = null;
        $this->_minifier = null;
        $this->_logger = null;
        $this->_staticViewDir = null;
        $this->_baseUrl = null;
        $this->_model = null;
    }

    /**
     * @param string $originalFile
     * @param string $expectedUrl
     * @dataProvider getUrlDataProvider
     */
    public function testGetUrl($originalFile, $expectedUrl)
    {
        $this->_prepareProcessMock($originalFile);
        $this->assertSame($expectedUrl, $this->_model->getUrl());
        $this->assertSame($expectedUrl, $this->_model->getUrl());
    }

    /**
     * @return array
     */
    public static function getUrlDataProvider()
    {
        return array(
            'already minified' => array(
                self::ORIG_PREMINIFIED_FILE,
                self::ORIG_PREMINIFIED_URL,
            ),
            'needs minification' => array(
                self::ORIG_FILE,
                self::MINIFIED_URL,
            ),
        );
    }

    /**
     * @param string $originalFile
     * @param string $expectedSourceFile
     * @dataProvider getSourceFileDataProvider
     */
    public function testGetSourceFile($originalFile, $expectedSourceFile)
    {
        $this->_prepareProcessMock($originalFile);
        $this->assertSame($expectedSourceFile, $this->_model->getSourceFile());
        $this->assertSame($expectedSourceFile, $this->_model->getSourceFile());
    }

    /**
     * @return array
     */
    public static function getSourceFileDataProvider()
    {
        return array(
            'already minified' => array(
                self::ORIG_PREMINIFIED_FILE,
                self::ORIG_PREMINIFIED_FILE,
            ),
            'needs minification' => array(
                self::ORIG_FILE,
                self::MINIFIED_FILE,
            ),
        );
    }

    /**
     * @param string $originalFile
     * @param string $expectedPath
     * @dataProvider getRelativePathDataProvider
     */
    public function testGetRelativePath($originalFile, $expectedPath)
    {
        $this->_prepareProcessMock($originalFile);
        $this->assertSame($expectedPath, $this->_model->getRelativePath());
        $this->assertSame($expectedPath, $this->_model->getRelativePath());
    }

    /**
     * @return array
     */
    public static function getRelativePathDataProvider()
    {
        return array(
            'already minified' => array(
                self::ORIG_PREMINIFIED_FILE,
                self::ORIG_PREMINIFIED_RELPATH,
            ),
            'needs minification' => array(
                self::ORIG_FILE,
                self::MINIFIED_RELPATH,
            ),
        );
    }

    /**
     * Prepare mocked system to be used in tests
     *
     * @param $originalFile
     */
    protected function _prepareProcessMock($originalFile)
    {
        $this->_asset->expects($this->once())
            ->method('getSourceFile')
            ->will($this->returnValue($originalFile));

        $url = ($originalFile == self::ORIG_FILE) ? self::ORIG_URL : self::ORIG_PREMINIFIED_URL;
        $this->_asset->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue($url));

        $relPath = ($originalFile == self::ORIG_FILE) ? self::ORIG_RELPATH : self::ORIG_PREMINIFIED_RELPATH;
        $this->_asset->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnValue($relPath));

        $this->_minifier->expects($this->once())
            ->method('getMinifiedFile')
            ->will($this->returnValueMap([
                [self::ORIG_FILE, self::MINIFIED_FILE],
                [self::ORIG_PREMINIFIED_FILE, self::ORIG_PREMINIFIED_FILE],
            ]));

        $this->_staticViewDir->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnValueMap([
                [self::ORIG_FILE, self::ORIG_RELPATH],
                [self::MINIFIED_FILE, self::MINIFIED_RELPATH],
            ]));

        $this->_baseUrl->expects($this->any())
            ->method('getBaseUrl')
            ->with(array('_type' => \Magento\UrlInterface::URL_TYPE_STATIC))
            ->will($this->returnValue(self::STATIC_BASE_URL));
    }

    public function testProcessException()
    {
        $this->_asset->expects($this->once())
            ->method('getSourceFile')
            ->will($this->returnValue(self::ORIG_FILE));
        $this->_asset->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue(self::ORIG_URL));
        $this->_asset->expects($this->once())
            ->method('getRelativePath')
            ->will($this->returnValue(self::ORIG_RELPATH));

        $this->_minifier->expects($this->once())
            ->method('getMinifiedFile')
            ->with(self::ORIG_FILE)
            ->will($this->throwException(new \Exception('Error')));

        $this->assertSame(self::ORIG_URL, $this->_model->getUrl());
        $this->assertSame(self::ORIG_RELPATH, $this->_model->getRelativePath());
        $this->assertSame(self::ORIG_FILE, $this->_model->getSourceFile());
    }

    public function testGetContent()
    {
        $contentType = 'content_type';
        $this->_asset->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue($contentType));
        $this->assertSame($contentType, $this->_model->getContentType());
    }
}
