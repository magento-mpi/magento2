<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\State|\PHPUnit_Framework_MockObject_MockObject
     */
    private $appState;

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
     * @var \Magento\View\Service\PreProcessing\Cache|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cachePreProcessing;

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
     * @var \Magento\View\Service
     */
    private $object;

    protected function setUp()
    {
        $this->appState = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->cachePreProcessing = $this->getMock(
            'Magento\View\Service\PreProcessing\Cache', array(), array(), '', false
        );
        $this->preprocessorFactory = $this->getMock(
            'Magento\View\Asset\PreProcessor\Factory', array(), array(), '', false
        );
        $this->viewFileResolution = $this->getMock(
            'Magento\View\Design\FileResolution\Fallback', array(), array(), '', false
        );
        $this->theme = $this->getMockForAbstractClass('Magento\View\Design\ThemeInterface');

        $cacheFactory = $this->getMock('Magento\View\Service\PreProcessing\CacheFactory', array(), array(), '', false);
        $cacheFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->cachePreProcessing));

        $themeProvider = $this->getMock('Magento\View\Design\Theme\Provider', array(), array(), '', false);
        $themeProvider->expects($this->any())
            ->method('getThemeModel')
            ->with('magento_theme', 'frontend')
            ->will($this->returnValue($this->theme));

        $this->initFilesystem();

        $this->object = new \Magento\View\Service(
            $cacheFactory,
            $this->appState,
            $this->filesystem,
            $this->preprocessorFactory,
            $this->viewFileResolution,
            $themeProvider
        );
    }

    /**
     * @param string $mode
     * @param bool $expected
     *
     * @dataProvider isPublishingDisallowedDataProvider
     */
    public function testIsPublishingDisallowed($mode, $expected)
    {
        $this->appState->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue($mode));
        $actual = $this->object->isPublishingDisallowed();
        $this->assertSame($expected, $actual);
    }

    /**
     * @return array
     */
    public function isPublishingDisallowedDataProvider()
    {
        return [
            'developer mode'   => [\Magento\App\State::MODE_DEVELOPER, true],
            'default mode'     => [\Magento\App\State::MODE_DEFAULT, false],
            'production mode'  => [\Magento\App\State::MODE_PRODUCTION, false],
            'nonexistent mode' => ['nonexistent', false],
        ];
    }

    public function testGetSourceFileNoOriginalFile()
    {
        $this->viewFileResolution->expects($this->once())
            ->method('getViewFile')
            ->with('frontend', $this->theme, 'en_US', 'some/file.ext', 'Magento_Module')
            ->will($this->returnValue(false));
        $this->assertFalse($this->object->getSourceFile($this->getAsset()));
    }

    public function testGetSourceFileCached()
    {
        $originalFile = '/root/some/file.ext';
        $expected = '/var/some/file.ext';
        $this->viewFileResolution->expects($this->once())
            ->method('getViewFile')
            ->with('frontend', $this->theme, 'en_US', 'some/file.ext', 'Magento_Module')
            ->will($this->returnValue($originalFile));
        $this->cachePreProcessing->expects($this->once())
            ->method('getProcessedFileFromCache')
            ->with($originalFile)
            ->will($this->returnValue($expected));
        $actual = $this->object->getSourceFile($this->getAsset());
        $this->assertSame($expected, $actual);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The requested asset type was 'ext', but ended up with 'ext2'
     */
    public function testGetSourceFileBadContentType()
    {
        $originalFile = '/root/some/file.ext2';
        $updatedContent = 'Updated content';
        $asset = $this->getAsset();
        $this->mockPreProcessing($asset, $originalFile, $updatedContent, 'ext2', 'ext2');
        $this->object->getSourceFile($asset);
    }

    /**
     * @param string $updatedContent
     * @param string $originalFile
     * @param string $originalContentType
     * @dataProvider getSourceFileDataProvider
     */
    public function testGetSourceFile($updatedContent, $originalFile, $originalContentType)
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

        $this->cachePreProcessing->expects($this->once())
            ->method('saveProcessedFileToCache')
            ->with($expected, $originalFile);

        $actual = $this->object->getSourceFile($asset);
        $this->assertSame($expected, $actual);
    }

    public function getSourceFileDataProvider()
    {
        return [
            'updated content' => ['Updated content', '/root/some/file.ext', 'ext'],
            'updated content type' => ["Content of '/root/some/file.ext'", '/root/some/file.ext2', 'ext2'],
            'updated both content and content type' => ['Updated content', '/root/some/file.ext2', 'ext2'],
        ];
    }

    public function testGetSourceFileNotChanged()
    {
        $originalFile = '/root/some/file.ext';
        $updatedContent = "Content of '/root/some/file.ext'";
        $asset = $this->getAsset();
        $this->mockPreProcessing($asset, $originalFile, $updatedContent, 'ext', 'ext');

        $this->varDir->expects($this->never())
            ->method('writeFile');

        $this->cachePreProcessing->expects($this->once())
            ->method('saveProcessedFileToCache')
            ->with($originalFile, $originalFile);

        $actual = $this->object->getSourceFile($asset);
        $this->assertSame($originalFile, $actual);
    }

    public function testPublishNotAllowed()
    {
        $this->appState->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(\Magento\App\State::MODE_DEVELOPER));
        $this->assertFalse($this->object->publish($this->getAsset()));
    }

    public function testPublishExistsBefore()
    {
        $this->appState->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(\Magento\App\State::MODE_PRODUCTION));
        $this->staticDirRead->expects($this->once())
            ->method('isExist')
            ->with('some/file.ext')
            ->will($this->returnValue(true));
        $this->assertTrue($this->object->publish($this->getAsset()));
    }

    public function testPublish()
    {
        $this->appState->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(\Magento\App\State::MODE_PRODUCTION));
        $this->staticDirRead->expects($this->once())
            ->method('isExist')
            ->with('some/file.ext')
            ->will($this->returnValue(false));

        $this->rootDirWrite->expects($this->once())
            ->method('getRelativePath')
            ->with('/root/some/file.ext')
            ->will($this->returnValue('some/file.ext'));
        $this->rootDirWrite->expects($this->once())
            ->method('copyFile')
            ->with('some/file.ext', 'some/file.ext', $this->staticDirWrite)
            ->will($this->returnValue(true));

        $this->assertTrue($this->object->publish($this->getAsset()));
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
     * @param \Magento\View\Asset\FileId|\PHPUnit_Framework_MockObject_MockObject $asset
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
        $this->cachePreProcessing->expects($this->once())
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
     * @return \Magento\View\Asset\FileId|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAsset()
    {
        $asset = $this->getMock('Magento\View\Asset\FileId', array(), array(), '', false);
        $asset->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue('magento_theme'));
        $asset->expects($this->any())
            ->method('getAreaCode')
            ->will($this->returnValue('frontend'));
        $asset->expects($this->any())
            ->method('getLocaleCode')
            ->will($this->returnValue('en_US'));
        $asset->expects($this->any())
            ->method('getFilePath')
            ->will($this->returnValue('some/file.ext'));
        $asset->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnValue('some/file.ext'));
        $asset->expects($this->any())
            ->method('getSourceFile')
            ->will($this->returnValue('/root/some/file.ext'));
        $asset->expects($this->any())
            ->method('getModule')
            ->will($this->returnValue('Magento_Module'));
        $asset->expects($this->any())
            ->method('getContentType')
            ->will($this->returnValue('ext'));

        return $asset;
    }
}
