<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\File;

use Magento\View\Asset\File;
use Magento\View\Asset\LocalInterface;

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
     * @var \Magento\View\Asset\File\Source\Cache
     */
    protected $cachePreProcessing;

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
     * @var \Magento\View\Design\FileResolution\Fallback\ViewFile
     */
    protected $viewFileResolution;

    /**
     * @var \Magento\View\Design\Theme\Provider
     */
    protected $themeProvider;

    /**
     * @param \Magento\View\Asset\File\Source\CacheFactory $cacheFactory
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\Asset\PreProcessor\Factory $preprocessorFactory
     * @param \Magento\View\Design\FileResolution\Fallback\ViewFile $viewFileResolution
     * @param \Magento\View\Design\Theme\Provider $themeProvider
     */
    public function __construct(
        \Magento\View\Asset\File\Source\CacheFactory $cacheFactory,
        \Magento\App\Filesystem $filesystem,
        \Magento\View\Asset\PreProcessor\Factory $preprocessorFactory,
        \Magento\View\Design\FileResolution\Fallback\ViewFile $viewFileResolution,
        \Magento\View\Design\Theme\Provider $themeProvider
    ) {
        $this->filesystem = $filesystem;
        $this->rootDir = $filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        $this->varDir = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);
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
     * @inheritdoc
     */
    public function getFile(File $asset)
    {
        $context = $asset->getContext();
        if ($context instanceof FallbackContext) {
            $sourceFile = $this->findFileThroughFallback($asset, $context);
        } else {
            $sourceFile = $this->findFile($asset, $context);
        }
        if ($sourceFile) {
            return $this->getProcessedFile($asset, $sourceFile);
        }
        return false;
    }

    /**
     * Find asset file via fallback mechanism
     *
     * @param File $asset
     * @param FallbackContext $context
     * @return bool|string
     */
    private function findFileThroughFallback(File $asset, FallbackContext $context)
    {
        $themeModel = $this->themeProvider->getThemeModel($context->getThemePath(), $context->getAreaCode());
        $sourceFile = $this->viewFileResolution->getViewFile(
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
     * @param Context $context
     * @return string
     */
    private function findFile(LocalInterface $asset, Context $context)
    {
        $dir = $this->filesystem->getDirectoryRead($context->getBaseDirType());
        return $dir->getAbsolutePath($asset->getRelativePath());
    }

    /**
     * @param LocalInterface $asset
     * @param string $sourceFile
     * @return bool|string
     * @throws \LogicException
     */
    private function getProcessedFile(LocalInterface $asset, $sourceFile)
    {
        $processedFile = $this->cachePreProcessing->getProcessedFileFromCache($sourceFile, $asset);
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
            $this->cachePreProcessing->saveProcessedFileToCache($processedFile, $sourceFile, $asset);
        }
        return $processedFile;
    }
}
