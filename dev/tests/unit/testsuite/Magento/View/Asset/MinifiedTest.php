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
    const STATIC_BASE_URL = 'http://localhost/static_dir/';
    const STATIC_PATH = '/home/htdocs/static_dir';

    const ORIG_PREMINIFIED_FILE = '/home/htdocs/some/other/dir/original.min.js';
    const ORIG_PREMINIFIED_RELPATH = 'other/dir/original.min.js';
    const ORIG_PREMINIFIED_URL = 'http://localhost/static_dir/other/dir/original.min.js';
    const ORIG_PREMINIFIED_ROOT_RELPATH = 'some/other/dir/original.min.js';

    const ORIG_HASMINIFIED_FILE = '/home/htdocs/some/other/dir/original.js';
    const ORIG_HASMINIFIED_RELPATH = 'other/dir/original.js';
    const ORIG_HASMINIFIED_URL = 'http://localhost/static_dir/other/dir/original.js';
    const ORIG_HASMINIFIED_ROOT_RELPATH = 'some/other/dir/original.js';

    const ORIG_FILE = '/home/htdocs/some/dir/original.js';
    const ORIG_RELPATH = 'dir/original.js';
    const ORIG_URL = 'http://localhost/static_dir/dir/original.js';
    const ORIG_ROOT_RELPATH = 'some/dir/original.js';
    const ORIG_FILE_MINIFIED_TRY = '/home/htdocs/some/dir/original.min.js';
    const ORIG_FILE_MINIFIED_TRY_ROOT_RELPATH = 'some/dir/original.min.js';
    const MINIFIED_URL_PATTERN = 'http://localhost/static_dir/_cache/minified/%s_original.min.js';
    const MINIFIED_FILE_PATTERN = '/home/htdocs/static_dir/_cache/minified/%s_original.min.js';
    const MINIFIED_RELPATH_PATTERN = '_cache/minified/%s_original.min.js';

    /**
     * @var \Magento\View\Asset\LocalInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_asset;

    /**
     * @var \Magento\Code\Minifier\StrategyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_strategy;

    /**
     * @var \Magento\Logger|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logger;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_staticViewDir;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_rootDir;

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
        $this->_strategy = $this->getMock('\Magento\Code\Minifier\StrategyInterface', array(),
            array(), '', false);
        $this->_logger = $this->getMock('\Magento\Logger', array(), array(), '', false);
        $this->_baseUrl = $this->getMock('\Magento\Url', array(), array(), '', false);
        $this->_staticViewDir = $this->getMock('\Magento\Filesystem\Directory\ReadInterface', array(), array(), '',
            false);
        $this->_rootDir = $this->getMock('\Magento\Filesystem\Directory\ReadInterface', array(), array(), '',
            false);

        $filesystem = $this->getMock('\Magento\App\Filesystem', array(), array(), '', false);
        $filesystem->expects($this->any())
            ->method('getDirectoryRead')
            ->will($this->returnValueMap([
                [\Magento\App\Filesystem::STATIC_VIEW_DIR, $this->_staticViewDir],
                [\Magento\App\Filesystem::ROOT_DIR, $this->_rootDir],
            ]));

        $this->_model = new \Magento\View\Asset\Minified($this->_asset, $this->_strategy, $this->_logger,
            $filesystem, $this->_baseUrl);
    }

    protected function tearDown()
    {
        $this->_asset = null;
        $this->_strategy = null;
        $this->_logger = null;
        $this->_staticViewDir = null;
        $this->_baseUrl = null;
        $this->_model = null;
    }

    /**
     * @param string $originalFile
     * @param string $expectedUrl
     * @param string $expectedFileToMinify
     * @dataProvider getUrlDataProvider
     */
    public function testGetUrl($originalFile, $expectedUrl, $expectedFileToMinify = null)
    {
        $this->_prepareProcessMock($originalFile, $expectedFileToMinify);
        $this->assertStringMatchesFormat($expectedUrl, $this->_model->getUrl());
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
            'has minified version' => array(
                self::ORIG_HASMINIFIED_FILE,
                self::ORIG_PREMINIFIED_URL,
            ),
            'needs minification' => array(
                self::ORIG_FILE,
                self::MINIFIED_URL_PATTERN,
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
        $this->assertStringMatchesFormat($expectedSourceFile, $this->_model->getSourceFile());
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
            'has minified version' => array(
                self::ORIG_HASMINIFIED_FILE,
                self::ORIG_PREMINIFIED_FILE,
            ),
            'needs minification' => array(
                self::ORIG_FILE,
                self::MINIFIED_FILE_PATTERN,
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
        $this->assertStringMatchesFormat($expectedPath, $this->_model->getRelativePath());
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
            'has minified version' => array(
                self::ORIG_HASMINIFIED_FILE,
                self::ORIG_PREMINIFIED_RELPATH,
            ),
            'needs minification' => array(
                self::ORIG_FILE,
                self::MINIFIED_RELPATH_PATTERN,
            ),
        );
    }

    /**
     * Prepare mocked system to be used in tests
     *
     * @param string $originalFile
     * @throws \Exception
     */
    protected function _prepareProcessMock($originalFile)
    {
        switch ($originalFile) {
            case self::ORIG_PREMINIFIED_FILE:
                $relPath = self::ORIG_PREMINIFIED_RELPATH;
                $url = self::ORIG_PREMINIFIED_URL;
                break;
            case self::ORIG_HASMINIFIED_FILE:
                $relPath = self::ORIG_HASMINIFIED_RELPATH;
                $url = self::ORIG_HASMINIFIED_URL;
                break;
            case self::ORIG_FILE:
                $relPath = self::ORIG_RELPATH;
                $url = self::ORIG_URL;
                break;
            default:
                throw new \Exception("Invalid original file to setup the environment: {$originalFile}");
        }
        $this->_asset->expects($this->atLeastOnce())
            ->method('getSourceFile')
            ->will($this->returnValue($originalFile));
        $this->_asset->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnValue($relPath));
        $this->_asset->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue($url));

        $this->_rootDir->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnValueMap([
                [self::ORIG_PREMINIFIED_FILE, self::ORIG_PREMINIFIED_ROOT_RELPATH],
                [self::ORIG_HASMINIFIED_FILE, self::ORIG_HASMINIFIED_ROOT_RELPATH],
                [self::ORIG_FILE, self::ORIG_ROOT_RELPATH],
                [self::ORIG_FILE_MINIFIED_TRY, self::ORIG_FILE_MINIFIED_TRY_ROOT_RELPATH],
            ]));
        $this->_rootDir->expects($this->any())
            ->method('isExist')
            ->will($this->returnValueMap([
                [self::ORIG_PREMINIFIED_ROOT_RELPATH, $originalFile == self::ORIG_HASMINIFIED_FILE],
                [self::ORIG_FILE_MINIFIED_TRY_ROOT_RELPATH, false],
            ]));

        if ($originalFile == self::ORIG_FILE) {
            $this->_strategy->expects($this->once())
                ->method('minifyFile')
                ->with(self::ORIG_ROOT_RELPATH);
        } else {
            $this->_strategy->expects($this->never())
                ->method('minifyFile');
        }

        $this->_staticViewDir->expects($this->any())
            ->method('getAbsolutePath')
            ->will($this->returnCallback(
                function ($relPath) {
                    return self::STATIC_PATH . '/' . $relPath;
                }
            ));

        $this->_baseUrl->expects($this->any())
            ->method('getBaseUrl')
            ->with(array('_type' => \Magento\UrlInterface::URL_TYPE_STATIC))
            ->will($this->returnValue(self::STATIC_BASE_URL));
    }

    public function testProcessException()
    {
        $this->_asset->expects($this->atLeastOnce())
            ->method('getSourceFile')
            ->will($this->returnValue(self::ORIG_FILE));
        $this->_asset->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue(self::ORIG_URL));
        $this->_asset->expects($this->atLeastOnce())
            ->method('getRelativePath')
            ->will($this->returnValue(self::ORIG_RELPATH));

        $this->_strategy->expects($this->once())
            ->method('minifyFile')
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
