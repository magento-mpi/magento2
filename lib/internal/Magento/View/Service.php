<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

/**
 * A repository for view assets
 *
 * @see \Magento\View\Asset\AssetInterface
 */
class Service implements Asset\SourceFileInterface, Asset\PublishInterface
{
    /**
     * A suffix for temporary materialization directory where pre-processed files will be written (if necessary)
     */
    const TMP_MATERIALIZATION_DIR = 'view_preprocessed';

    /**#@+
     * Public directories prefix group
     */
    const PUBLIC_VIEW_DIR   = '_view';
    const PUBLIC_THEME_DIR  = '_theme';
    /**#@-*/

    /**
     * @var Service\PreProcessing\Cache
     */
    protected $cachePreProcessing;

    /**
     * @var \Magento\App\State
     */
    protected $appState;

    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $rootDir;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $varDir;

    /**
     * @var \Magento\View\Asset\PreProcessor\Factory
     */
    private $preprocessorFactory;

    /**
     * @var \Magento\View\Design\FileResolution\Fallback
     */
    protected $viewFileResolution;

    /**
     * @var \Magento\View\Design\Theme\Provider
     */
    protected $themeProvider;

    /**
     * @param Service\PreProcessing\CacheFactory $cacheFactory
     * @param \Magento\App\State $appState
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\Asset\PreProcessor\Factory $preprocessorFactory
     * @param \Magento\View\Design\FileResolution\Fallback $viewFileResolution
     * @param Design\Theme\Provider $themeProvider
     */
    public function __construct(
        \Magento\View\Service\PreProcessing\CacheFactory $cacheFactory,
        \Magento\App\State $appState,
        \Magento\App\Filesystem $filesystem,
        Asset\PreProcessor\Factory $preprocessorFactory,
        Design\FileResolution\Fallback $viewFileResolution,
        \Magento\View\Design\Theme\Provider $themeProvider
    ) {
        $this->appState = $appState;
        $this->filesystem = $filesystem;
        $this->rootDir = $this->filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        $this->varDir = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);
        $this->cachePreProcessing = $cacheFactory->create(
            $this->rootDir,
            array(
                '%root%' => $this->rootDir,
                '%var%'  => $this->varDir,
            )
        );
        $this->preprocessorFactory = $preprocessorFactory;
        $this->viewFileResolution = $viewFileResolution;
        $this->themeProvider = $themeProvider;
    }

    /**
     * Whether it is prohibited publishing view assets
     *
     * @return bool
     */
    public function isPublishingDisallowed()
    {
        return $this->appState->getMode() === \Magento\App\State::MODE_DEVELOPER;
    }

    /**
     * Determine the original source file for an asset
     *
     * The original source file is always different from what asset "claims" or it may not even exist.
     * This method will either locate the original file and process (materialize) it if necessary.
     * Materialization will occur only if result of preprocessing is different from the originally located file.
     */
    public function getSourceFile(Asset\LocalInterface $asset)
    {
        $assetSourceFile = $this->getOriginalSourceFile($asset);
        if ($assetSourceFile) {
            $assetSourceFile = $this->getProcessedFile($asset, $assetSourceFile);
        }
        return $assetSourceFile;
    }

    /**
     * @param Asset\FileId $asset
     * @return bool|string
     */
    private function getOriginalSourceFile(Asset\FileId $asset)
    {
        $themeModel = $this->themeProvider->getThemeModel($asset->getThemePath(), $asset->getAreaCode());
        $sourceFile = $this->viewFileResolution->getViewFile(
            $asset->getAreaCode(),
            $themeModel,
            $asset->getLocaleCode(),
            $asset->getFilePath(),
            $asset->getModule()
        );
        return $sourceFile;
    }

    /**
     * @param Asset\FileId $asset
     * @param string $sourceFile
     * @return bool|string
     * @throws \LogicException
     */
    private function getProcessedFile(Asset\FileId $asset, $sourceFile)
    {
        $processedFile = $this->cachePreProcessing->getProcessedFileFromCache($sourceFile);
        if (!$processedFile) {
            $processedFile = $sourceFile;
            $origContent = $this->rootDir->readFile($this->rootDir->getRelativePath($sourceFile));
            $origContentType = pathinfo($sourceFile, PATHINFO_EXTENSION);
            $targetContentType = $asset->getContentType();
            $content = $origContent;
            $contentType = $origContentType;
            $preProcessors = $this->preprocessorFactory->getPreProcessors($origContentType, $targetContentType);
            foreach ($preProcessors as $processor) {
                list($content, $contentType) = $processor->process($content, $contentType, $asset);
            }
            if ($contentType !== $targetContentType) {
                // impose an integrity check to avoid generating mismatching content type
                throw new \LogicException(
                    "The requested asset type was '{$targetContentType}', but ended up with '{$contentType}'"
                );
            }
            if ($origContent != $content || $origContentType != $contentType) {
                $relPath = self::TMP_MATERIALIZATION_DIR . '/' . $asset->getRelativePath();
                $processedFile = $this->varDir->getAbsolutePath() . '/' . $relPath;
                $this->varDir->writeFile($relPath, $content);
            }
            $this->cachePreProcessing->saveProcessedFileToCache($processedFile, $sourceFile);
        }
        return $processedFile;
    }

    /**
     * @inheritdoc
     */
    public function publish(Asset\LocalInterface $asset)
    {
        if ($this->isPublishingDisallowed()) {
            return false;
        }
        $dir = $this->filesystem->getDirectoryRead(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        if ($dir->isExist($asset->getRelativePath())) {
            return true;
        }
        return $this->publishAsset($asset);
    }

    /**
     * Publish the asset
     *
     * @param Asset\LocalInterface $asset
     * @return bool
     */
    private function publishAsset(Asset\LocalInterface $asset)
    {
        $dir = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        $rootDir = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $source = $rootDir->getRelativePath($asset->getSourceFile());
        $destination = $asset->getRelativePath();
        return $rootDir->copyFile($source, $destination, $dir);
    }
}
