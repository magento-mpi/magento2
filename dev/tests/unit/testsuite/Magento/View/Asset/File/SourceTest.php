<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\File;

class SourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filesystem;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rootDirRead;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rootDirWrite;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $varDir;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $staticDirRead;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $staticDirWrite;

    /**
     * @var \Magento\View\Asset\File\Source\Cache|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * @var \Magento\View\Asset\PreProcessor\Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $preprocessorFactory;

    /**
     * @var \Magento\View\Design\FileResolution\Fallback|\PHPUnit_Framework_MockObject_MockObject
     */
    private $viewFileResolution;

    /**
     * @var \Magento\View\Design\ThemeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $theme;

    /**
     * @var Source
     */
    private $object;

    protected function setUp()
    {
        $this->cache = $this->getMock(
            'Magento\View\Asset\File\Source\Cache', array(), array(), '', false
        );
        $this->preprocessorFactory = $this->getMock(
            'Magento\View\Asset\PreProcessor\Factory', array(), array(), '', false
        );
        $this->viewFileResolution = $this->getMock(
            'Magento\View\Design\FileResolution\Fallback', array(), array(), '', false
        );
        $this->theme = $this->getMockForAbstractClass('Magento\View\Design\ThemeInterface');

        $cacheFactory = $this->getMock('Magento\View\Asset\File\Source\CacheFactory', array(), array(), '', false);
        $cacheFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->cache));

        $themeProvider = $this->getMock('Magento\View\Design\Theme\Provider', array(), array(), '', false);
        $themeProvider->expects($this->any())
            ->method('getThemeModel')
            ->with('magento_theme', 'frontend')
            ->will($this->returnValue($this->theme));

        $this->initFilesystem();

        $this->object = new Source(
            $cacheFactory,
            $this->filesystem,
            $this->preprocessorFactory,
            $this->viewFileResolution,
            $themeProvider
        );
    }

    public function testGetFileNoOriginalFile()
    {
        $this->viewFileResolution->expects($this->once())
            ->method('getViewFile')
            ->with('frontend', $this->theme, 'en_US', 'some/file.ext', 'Magento_Module')
            ->will($this->returnValue(false));
        $this->assertFalse($this->object->getFile($this->getAsset()));
    }

    public function testGetFileNoOriginalFileBasic()
    {
        $this->staticDirRead->expects($this->once())
            ->method('getAbsolutePath')
            ->with('some/file.ext')
            ->will($this->returnValue(false));
        $this->assertFalse($this->object->getFile($this->getAsset(false)));
    }

    public function testGetFileCached()
    {
        $originalFile = '/root/some/file.ext';
        $expected = '/var/some/file.ext';
        $this->viewFileResolution->expects($this->once())
            ->method('getViewFile')
            ->with('frontend', $this->theme, 'en_US', 'some/file.ext', 'Magento_Module')
            ->will($this->returnValue($originalFile));
        $this->cache->expects($this->once())
            ->method('getProcessedFileFromCache')
            ->with($originalFile)
            ->will($this->returnValue($expected));
        $actual = $this->object->getFile($this->getAsset());
        $this->assertSame($expected, $actual);
    }

    public function testGetFileCachedBasic()
    {
        $originalFile = '/root/some/file.ext';
        $expected = '/var/some/file.ext';
        $this->staticDirRead->expects($this->once())
            ->method('getAbsolutePath')
            ->with('some/file.ext')
            ->will($this->returnValue($originalFile));
        $this->cache->expects($this->once())
            ->method('getProcessedFileFromCache')
            ->with($originalFile)
            ->will($this->returnValue($expected));
        $actual = $this->object->getFile($this->getAsset(false));
        $this->assertSame($expected, $actual);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The requested asset type was 'ext', but ended up with 'ext2'
     */
    public function testGetFileBadContentType()
    {
        $originalFile = '/root/some/file.ext2';
        $updatedContent = 'Updated content';
        $asset = $this->getAsset();
        $this->mockPreProcessing($asset, $originalFile, $updatedContent, 'ext2', 'ext2');
        $this->object->getFile($asset);
    }

    /**
     * @param string $updatedContent
     * @param string $originalFile
     * @param string $originalContentType
     * @dataProvider getFileDataProvider
     */
    public function testGetFile($updatedContent, $originalFile, $originalContentType)
    {
        $expected = '/var/view_preprocessed/some/file.ext';
        $asset = $this->getAsset();
        $this->mockPreProcessing($asset, $originalFile, $updatedContent, $originalContentType, 'ext');

        $this->varDir->expects($this->any())
            ->method('getAbsolutePath')
            ->will($this->returnValue('/var'));
        $this->varDir->expects($this->once())
            ->method('writeFile')
            ->with('view_preprocessed/some/file.ext', $updatedContent);

        $this->cache->expects($this->once())
            ->method('saveProcessedFileToCache')
            ->with($expected, $originalFile);

        $actual = $this->object->getFile($asset);
        $this->assertSame($expected, $actual);
    }

    public function getFileDataProvider()
    {
        return [
            'updated content' => ['Updated content', '/root/some/file.ext', 'ext'],
            'updated content type' => ["Content of '/root/some/file.ext'", '/root/some/file.ext2', 'ext2'],
            'updated both content and content type' => ['Updated content', '/root/some/file.ext2', 'ext2'],
        ];
    }

    public function testGetFileNotChanged()
    {
        $originalFile = '/root/some/file.ext';
        $updatedContent = "Content of '/root/some/file.ext'";
        $asset = $this->getAsset();
        $this->mockPreProcessing($asset, $originalFile, $updatedContent, 'ext', 'ext');

        $this->varDir->expects($this->never())
            ->method('writeFile');

        $this->cache->expects($this->once())
            ->method('saveProcessedFileToCache')
            ->with($originalFile, $originalFile);

        $actual = $this->object->getFile($asset);
        $this->assertSame($originalFile, $actual);
    }

    protected function initFilesystem()
    {
        $this->filesystem = $this->getMock('Magento\App\Filesystem', array(), array(), '', false);
        $this->rootDirRead = $this->getMockForAbstractClass('Magento\Filesystem\Directory\ReadInterface');
        $this->rootDirWrite = $this->getMockForAbstractClass('Magento\Filesystem\Directory\WriteInterface');
        $this->staticDirRead = $this->getMockForAbstractClass('Magento\Filesystem\Directory\ReadInterface');
        $this->staticDirWrite = $this->getMockForAbstractClass('Magento\Filesystem\Directory\WriteInterface');
        $this->varDir = $this->getMockForAbstractClass('Magento\Filesystem\Directory\WriteInterface');

        $readDirMap = [
            [\Magento\App\Filesystem::ROOT_DIR, $this->rootDirRead],
            [\Magento\App\Filesystem::STATIC_VIEW_DIR, $this->staticDirRead],
        ];
        $writeDirMap = [
            [\Magento\App\Filesystem::ROOT_DIR, $this->rootDirWrite],
            [\Magento\App\Filesystem::STATIC_VIEW_DIR, $this->staticDirWrite],
            [\Magento\App\Filesystem::VAR_DIR, $this->varDir],
        ];

        $this->filesystem->expects($this->any())
            ->method('getDirectoryRead')
            ->will($this->returnValueMap($readDirMap));
        $this->filesystem->expects($this->any())
            ->method('getDirectoryWrite')
            ->will($this->returnValueMap($writeDirMap));
    }

    /**
     * @param \Magento\View\Asset\File|\PHPUnit_Framework_MockObject_MockObject $asset
     * @param string $originalFile
     * @param string $updatedContent
     * @param string $originalContentType
     * @param string $updatedContentType
     */
    protected function mockPreProcessing(
        $asset, $originalFile, $updatedContent, $originalContentType, $updatedContentType
    ) {
        $this->viewFileResolution->expects($this->once())
            ->method('getViewFile')
            ->with('frontend', $this->theme, 'en_US', 'some/file.ext', 'Magento_Module')
            ->will($this->returnValue($originalFile));
        $this->cache->expects($this->once())
            ->method('getProcessedFileFromCache')
            ->with($originalFile)
            ->will($this->returnValue(false));
        $this->rootDirRead->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnArgument(0));
        $this->rootDirRead->expects($this->any())
            ->method('readFile')
            ->will($this->returnCallback(function ($file) {
                return "Content of '$file'";
            }));

        $processor = $this->getMockForAbstractClass('Magento\View\Asset\PreProcessorInterface');
        $processor->expects($this->once())
            ->method('process')
            ->with("Content of '$originalFile'", $originalContentType, $asset)
            ->will($this->returnValue([$updatedContent, $updatedContentType]));
        $this->preprocessorFactory->expects($this->once())
            ->method('getPreProcessors')
            ->with($originalContentType, 'ext')
            ->will($this->returnValue([$processor]));
    }

    /**
     * Create an asset mock
     *
     * @param bool $isFallback
     * @return \Magento\View\Asset\File|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAsset($isFallback = true)
    {
        if ($isFallback) {
            $context = new FallbackContext(
                'http://example.com/static/',
                'frontend',
                'magento_theme',
                'en_US'
            );
        } else {
            $context = new Context('http://example.com/static/', \Magento\App\Filesystem::STATIC_VIEW_DIR, '');
        }

        $asset = $this->getMock('Magento\View\Asset\File', array(), array(), '', false);
        $asset->expects($this->any())
            ->method('getContext')
            ->will($this->returnValue($context));
        $asset->expects($this->any())
            ->method('getFilePath')
            ->will($this->returnValue('some/file.ext'));
        $asset->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnValue('some/file.ext'));
        $asset->expects($this->any())
            ->method('getModule')
            ->will($this->returnValue('Magento_Module'));
        $asset->expects($this->any())
            ->method('getContentType')
            ->will($this->returnValue('ext'));

        return $asset;
    }
}
