<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor;

use Magento\View\Asset\PreProcessor\PreProcessorInterface;
use Magento\Filesystem\Directory\WriteInterface;

/**
 * Css pre-processor url resolver
 */
class UrlResolver implements PreProcessorInterface
{
    /**
     * @var WriteInterface
     */
    protected $rootDirectory;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var \Magento\View\RelatedFile
     */
    protected $relatedFile;

    /**
     * Helper to process css content
     *
     * @var \Magento\View\Url\CssResolver
     */
    protected $cssUrlResolver;

    /**
     * @var \Magento\View\Publisher
     */
    protected $publisher;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param \Magento\View\RelatedFile $relatedFile
     * @param \Magento\View\Url\CssResolver $cssUrlResolver
     * @param \Magento\View\Publisher $publisher
     * @param \Magento\Logger $logger
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\View\RelatedFile $relatedFile,
        \Magento\View\Url\CssResolver $cssUrlResolver,
        \Magento\View\Publisher $publisher,
        \Magento\Logger $logger
    ) {
        $this->rootDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $this->viewFileSystem = $viewFileSystem;
        $this->relatedFile = $relatedFile;
        $this->cssUrlResolver = $cssUrlResolver;
        $this->publisher = $publisher;
        $this->logger = $logger;
    }

    /**
     * Process LESS file content
     *
     * @param \Magento\View\Publisher\FileInterface $publisherFile
     * @param \Magento\Filesystem\Directory\WriteInterface $targetDirectory
     * @return string
     */
    public function process(\Magento\View\Publisher\FileInterface $publisherFile, $targetDirectory)
    {
        if (!$publisherFile->isPublicationAllowed()) {
            return $publisherFile->getSourcePath();
        }
        $filePath = $this->viewFileSystem->normalizePath($publisherFile->getFilePath());
        $sourcePath = $this->viewFileSystem->normalizePath($publisherFile->getSourcePath());
        $targetPath = $this->publisher->buildPublicViewFilename($publisherFile);
        $content = $this->rootDirectory->readFile($this->rootDirectory->getRelativePath($sourcePath));
        $params = $publisherFile->getViewParams();

        $callback = function ($fileId) use ($filePath, $params) {
            $relatedPathPublic = $this->publishRelatedViewFile($fileId, $filePath, $params);
            return $relatedPathPublic;
        };
        try {
            $content = $this->cssUrlResolver->replaceCssRelativeUrls($content, $sourcePath, $targetPath, $callback);
        } catch (\Magento\Exception $e) {
            $this->logger->logException($e);
        }

        $tmpFilePath = 'view' . '/' .  $publisherFile->getPublicationPath();
        $targetDirectory->writeFile($tmpFilePath, $content);
        return $targetDirectory->getAbsolutePath($tmpFilePath);
    }

    /**
     * Publish file identified by $fileId basing on information about parent file path and name.
     *
     * @param string $fileId URL to the file that was extracted from $parentFilePath
     * @param string $parentFileName original file name identifier that was requested for processing
     * @param array $params theme/module parameters array
     * @return string
     */
    protected function publishRelatedViewFile($fileId, $parentFileName, $params)
    {
        $relativeFilePath = $this->relatedFile->buildPath($fileId, $parentFileName, $params);
        return $this->publisher->getPublicFilePath($relativeFilePath, $params);
    }
}
