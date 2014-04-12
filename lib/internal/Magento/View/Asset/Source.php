<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * A service for preprocessing content of assets
 */
class Source
{
    /**
     * A suffix for temporary materialization directory where pre-processed files will be written (if necessary)
     */
    const TMP_MATERIALIZATION_DIR = 'view_preprocessed';

    /**
     * @var \Magento\App\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\View\Asset\PreProcessor\Cache
     */
    protected $cache;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $rootDir;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $varDir;

    /**
     * @var \Magento\View\Asset\PreProcessor\Pool
     */
    private $preProcessorPool;

    /**
     * @var \Magento\View\Design\FileResolution\Fallback\StaticFile
     */
    protected $fallback;

    /**
     * @var \Magento\View\Design\Theme\Provider
     */
    protected $themeProvider;

    public function __construct(
        \Magento\View\Asset\PreProcessor\Cache $cache,
        \Magento\App\Filesystem $filesystem,
        \Magento\View\Asset\PreProcessor\Pool $preProcessorPool,
        \Magento\View\Design\FileResolution\Fallback\StaticFile $fallback,
        \Magento\View\Design\Theme\Provider $themeProvider
    ) {
        $this->cache = $cache;
        $this->filesystem = $filesystem;
        $this->rootDir = $filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        $this->varDir = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);
        $this->preProcessorPool = $preProcessorPool;
        $this->fallback = $fallback;
        $this->themeProvider = $themeProvider;
    }

    /**
     * Get absolute path to the asset file
     *
     * @param LocalInterface $asset
     * @return bool|string
     */
    public function getFile(LocalInterface $asset)
    {
        $result = $this->preProcess($asset);
        if (!$result) {
            return false;
        }
        list($dirCode, $path) = $result;
        return $this->filesystem->getDirectoryRead($dirCode)->getAbsolutePath($path);
    }

    /**
     * Get content of an asset
     *
     * @param LocalInterface $asset
     * @return bool|string
     */
    public function getContent(LocalInterface $asset)
    {
        $result = $this->preProcess($asset);
        if (!$result) {
            return false;
        }
        list($dirCode, $path) = $result;
        return $this->filesystem->getDirectoryRead($dirCode)->readFile($path);
    }

    /**
     * Perform necessary preprocessing and materialization when the specified request is requested
     *
     * Returns an array of two elements:
     * - directory code where the file is supposed to be found
     * - relative path to the file
     *
     * Automatically caches the obtained successful results or returns false if source file was not found
     *
     * @param LocalInterface $asset
     * @return array|bool
     */
    private function preProcess(LocalInterface $asset)
    {
        $sourceFile = $this->findSourceFile($asset);
        if (!$sourceFile) {
            return false;
        }
        $dirCode = \Magento\App\Filesystem::ROOT_DIR;
        $path = $this->rootDir->getRelativePath($sourceFile);
        $cacheId = $path . ':' . $asset->getPath();
        $cached = $this->cache->load($cacheId);
        if ($cached) {
            return unserialize($cached);
        }
        $chain = new PreProcessor\Chain($asset, $this->rootDir->readFile($path), pathinfo($path, PATHINFO_EXTENSION));
        $preProcessors = $this->preProcessorPool
            ->getPreProcessors($chain->getOrigContentType(), $chain->getTargetContentType());
        foreach ($preProcessors as $processor) {
            $processor->process($chain);
        }
        $chain->assertValid();
        if ($chain->isMaterializationRequired()) {
            $dirCode = \Magento\App\Filesystem::VAR_DIR;
            $path = self::TMP_MATERIALIZATION_DIR . '/' . $asset->getPath();
            $this->varDir->writeFile($path, $chain->getContent());
        }
        $result = array($dirCode, $path);
        $this->cache->save(serialize($result), $cacheId);
        return $result;
    }

    /**
     * Search for asset file depending on its context type
     *
     * @param LocalInterface $asset
     * @return bool|string
     * @throws \LogicException
     */
    private function findSourceFile(LocalInterface $asset)
    {
        $context = $asset->getContext();
        if ($context instanceof File\FallbackContext) {
            $result = $this->findFileThroughFallback($asset, $context);
        } elseif ($context instanceof File\Context) {
            $result = $this->findFile($asset, $context);
        } else {
            $type = get_class($context);
            throw new \LogicException("Support for {$type} is not implemented.");
        }
        return $result;
    }

    /**
     * Find asset file via fallback mechanism
     *
     * @param LocalInterface $asset
     * @param File\FallbackContext $context
     * @return bool|string
     */
    private function findFileThroughFallback(LocalInterface $asset, File\FallbackContext $context)
    {
        $themeModel = $this->themeProvider->getThemeModel($context->getThemePath(), $context->getAreaCode());
        $sourceFile = $this->fallback->getFile(
            $context->getAreaCode(),
            $themeModel,
            $context->getLocaleCode(),
            $asset->getFilePath(),
            $asset->getModule()
        );
        return $sourceFile;
    }

    /**
     * Find asset file by simply appending its path to the directory in context
     *
     * @param LocalInterface $asset
     * @param File\Context $context
     * @return string
     */
    private function findFile(LocalInterface $asset, File\Context $context)
    {
        $dir = $this->filesystem->getDirectoryRead($context->getBaseDirType());
        return $dir->getAbsolutePath($asset->getPath());
    }
}
