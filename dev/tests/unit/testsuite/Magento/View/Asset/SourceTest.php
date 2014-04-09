<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

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
     * @var \Magento\View\Asset\PreProcessor\Cache|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * @var \Magento\View\Asset\PreProcessor\Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $preprocessorFactory;

    /**
     * @var \Magento\View\Design\FileResolution\Fallback\ViewFile|\PHPUnit_Framework_MockObject_MockObject
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
            'Magento\View\Asset\PreProcessor\Cache', array(), array(), '', false
        );
        $this->preprocessorFactory = $this->getMock(
            'Magento\View\Asset\PreProcessor\Factory', array(), array(), '', false
        );
        $this->viewFileResolution = $this->getMock(
            'Magento\View\Design\FileResolution\Fallback\ViewFile', array(), array(), '', false
        );
        $this->theme = $this->getMockForAbstractClass('Magento\View\Design\ThemeInterface');

        $themeProvider = $this->getMock('Magento\View\Design\Theme\Provider', array(), array(), '', false);
        $themeProvider->expects($this->any())
            ->method('getThemeModel')
            ->with('magento_theme', 'frontend')
            ->will($this->returnValue($this->theme));

        $this->initFilesystem();

        $this->object = new Source(
            $this->cache,
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
        $root = '/root/some/file.ext';
        $expected = '/var/some/file.ext';
        $filePath = 'some/file.ext';
        $this->viewFileResolution->expects($this->once())
            ->method('getViewFile')
            ->with('frontend', $this->theme, 'en_US', $filePath, 'Magento_Module')
            ->will($this->returnValue($root));
        $this->rootDirRead->expects($this->once())
            ->method('getRelativePath')
            ->with($root)
            ->will($this->returnValue($filePath));
        $this->cache->expects($this->once())
            ->method('load')
            ->with("some/file.ext:{$filePath}")
            ->will($this->returnValue(serialize(array(\Magento\App\Filesystem::VAR_DIR, $filePath))));

        $this->varDir->expects($this->once())->method('getAbsolutePath')
            ->with($filePath)
            ->will($this->returnValue($expected));
        $this->assertSame($expected, $this->object->getFile($this->getAsset()));
    }

    /**
     * @param string $origFile
     * @param string $origPath
     * @param string $origContentType
     * @param string $isMaterialization
     * @dataProvider getFileDataProvider
     */
    public function testGetFile($origFile, $origPath, $origContentType, $isMaterialization)
    {
        $filePath = 'some/file.ext';
        $cacheValue = "{$origPath}:{$filePath}";
        $this->viewFileResolution->expects($this->once())
            ->method('getViewFile')
            ->with('frontend', $this->theme, 'en_US', $filePath, 'Magento_Module')
            ->will($this->returnValue($origFile));
        $this->rootDirRead->expects($this->once())
            ->method('getRelativePath')
            ->with($origFile)
            ->will($this->returnValue($origPath));
        $this->cache->expects($this->once())
            ->method('load')
            ->will($this->returnValue(false));
        $this->rootDirRead->expects($this->once())
            ->method('readFile')
            ->with($origPath)
            ->will($this->returnValue('content'));
        $processor = $this->getMockForAbstractClass('Magento\View\Asset\PreProcessorInterface');
        $this->preprocessorFactory->expects($this->once())
            ->method('getPreProcessors')
            ->with($origContentType, 'ext')
            ->will($this->returnValue([$processor]));
        $processor->expects($this->once())->method('process')->with($this->anything()); // with chain
        if ($isMaterialization) {
            $this->varDir->expects($this->once())
                ->method('writeFile')
                ->with('view_preprocessed/some/file.ext', 'content');
            $this->cache->expects($this->once())
                ->method('save')
                ->with(serialize([\Magento\App\Filesystem::VAR_DIR, 'view_preprocessed/some/file.ext']), $cacheValue);
            $this->varDir->expects($this->once())
                ->method('getAbsolutePath')
                ->with('view_preprocessed/some/file.ext')->will($this->returnValue('result'));
        } else {
            $this->varDir->expects($this->never())->method('writeFile');
            $this->cache->expects($this->once())
                ->method('save')
                ->with(serialize([\Magento\App\Filesystem::ROOT_DIR, 'some/file.ext']), $cacheValue);
            $this->rootDirRead->expects($this->once())
                ->method('getAbsolutePath')
                ->with('some/file.ext')
                ->will($this->returnValue('result'));
        }
        $this->assertSame('result', $this->object->getFile($this->getAsset()));
    }

    /**
     * @return array
     */
    public function getFileDataProvider()
    {
        return [
            ['/root/some/file.ext', 'some/file.ext', 'ext', false],
            ['/root/some/file.ext2', 'some/file.ext2', 'ext2', true],
        ];
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
            [\Magento\App\Filesystem::VAR_DIR, $this->varDir],
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
     * Create an asset mock
     *
     * @param bool $isFallback
     * @return \Magento\View\Asset\File|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAsset($isFallback = true)
    {
        if ($isFallback) {
            $context = new File\FallbackContext(
                'http://example.com/static/',
                'frontend',
                'magento_theme',
                'en_US'
            );
        } else {
            $context = new File\Context('http://example.com/static/', \Magento\App\Filesystem::STATIC_VIEW_DIR, '');
        }

        $asset = $this->getMock('Magento\View\Asset\File', array(), array(), '', false);
        $asset->expects($this->any())
            ->method('getContext')
            ->will($this->returnValue($context));
        $asset->expects($this->any())
            ->method('getFilePath')
            ->will($this->returnValue('some/file.ext'));
        $asset->expects($this->any())
            ->method('getPath')
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
