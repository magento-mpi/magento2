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
     * @var \Magento\App\State
     */
    protected $appState;

    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\View\Asset\PreProcessor\Factory
     */
    private $preprocessorFactory;

    /**
     * @var \Magento\View\Design\FileResolution\StrategyPool
     */
    protected $resolutionPool;

    /**
     * @var \Magento\View\Design\Theme\Provider
     */
    protected $themeProvider;

    /**
     * @param \Magento\App\State $appState
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\Asset\PreProcessor\Factory $preprocessorFactory
     * @param \Magento\View\Design\FileResolution\StrategyPool $resolutionPool
     * @param Design\Theme\Provider $themeProvider
     */
    public function __construct(
        \Magento\App\State $appState,
        \Magento\App\Filesystem $filesystem,
        Asset\PreProcessor\Factory $preprocessorFactory,
        Design\FileResolution\StrategyPool $resolutionPool,
        \Magento\View\Design\Theme\Provider $themeProvider
    ) {
        $this->appState = $appState;
        $this->filesystem = $filesystem;
        $this->preprocessorFactory = $preprocessorFactory;
        $this->resolutionPool = $resolutionPool;
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
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getSourceFile(Asset\LocalInterface $asset)
    {
        $cacheId = $asset->getRelativePath();
        $cacheHit = false;
        // TODO implement caching
        $file = false;
        if (!$cacheHit) {
            $file = $this->resolveAssetSource($asset);
            // TODO add result to cache (it can be false as well)
        }
        return $file;
    }

    /**
     * Determine the original source file for an asset
     *
     * The original source file is always different from what asset "claims" or it may not even exist.
     * This method will either locate the original file and process (materialize) it if necessary.
     * Materialization will occur only if result of preprocessing is different from the originally located file.
     *
     * @param Asset\FileId $asset
     * @return bool|string
     * @throws \LogicException
     */
    private function resolveAssetSource(Asset\FileId $asset)
    {
        $themeModel = $this->themeProvider->getThemeModel($asset->getThemePath(), $asset->getAreaCode());
        /**
         * Bypass proxy, since caching is out of scope of this method intentionally
         * @var Design\FileResolution\Strategy\Fallback $fallback
         */
        $fallback = $this->resolutionPool->getViewStrategy(true);
        $file = $fallback->getViewFile(
            $asset->getAreaCode(),
            $themeModel,
            $asset->getLocaleCode(),
            $asset->getFilePath(),
            $asset->getModule()
        );
        if ($file) {
            $origContent = file_get_contents($file);
            $origContentType = pathinfo($file, PATHINFO_EXTENSION);
            $targetContentType = $asset->getContentType();
            $content = $origContent;
            $contentType = $origContentType;
            foreach ($this->preprocessorFactory->getPreProcessors($origContentType, $targetContentType) as $processor) {
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
                $file = $this->filesystem->getPath(\Magento\App\Filesystem::VAR_DIR) . '/' . $relPath;
                $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR)->writeFile($relPath, $content);
            }
        }
        return $file;
    }

    /**
     * @inheritdoc
     */
    public function publish(Asset\LocalInterface $asset)
    {
        if ($this->appState->getMode() === \Magento\App\State::MODE_DEVELOPER) {
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
