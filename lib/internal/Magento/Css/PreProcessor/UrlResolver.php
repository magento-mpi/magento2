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
     * Temporary directory prefix
     */
    const TMP_RESOLVER_DIR   = 'resolver';

    /**
     * Root directory
     *
     * @var WriteInterface
     */
    protected $rootDirectory;

    /**
     * Related file
     *
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
     * @var \Magento\View\FileResolver
     */
    protected $fileResolver;

    /**
     * Logger
     *
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * Publisher file factory
     *
     * @var \Magento\View\Publisher\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\View\Service
     */
    protected $viewService;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\RelatedFile $relatedFile
     * @param \Magento\View\Url\CssResolver $cssUrlResolver
     * @param \Magento\View\FileResolver $fileResolver
     * @param \Magento\Logger $logger
     * @param \Magento\View\Publisher\FileFactory $fileFactory
     * @param \Magento\View\Service $viewService
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\RelatedFile $relatedFile,
        \Magento\View\Url\CssResolver $cssUrlResolver,
        \Magento\View\FileResolver $fileResolver,
        \Magento\Logger $logger,
        \Magento\View\Publisher\FileFactory $fileFactory,
        \Magento\View\Service $viewService
    ) {
        $this->rootDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $this->relatedFile = $relatedFile;
        $this->cssUrlResolver = $cssUrlResolver;
        $this->fileResolver = $fileResolver;
        $this->logger = $logger;
        $this->fileFactory = $fileFactory;
        $this->viewService = $viewService;
    }

    /**
     * Process LESS file content
     *
     * @param \Magento\View\Publisher\FileInterface $publisherFile
     * @param \Magento\Filesystem\Directory\WriteInterface $targetDirectory
     * @return \Magento\View\Publisher\FileInterface
     */
    public function process(\Magento\View\Publisher\FileInterface $publisherFile, $targetDirectory)
    {
        if (!$publisherFile->isPublicationAllowed()) {
            return $publisherFile;
        }
        $filePath = $publisherFile->getFilePath();
        $sourcePath = $publisherFile->getSourcePath();
        $content = $this->rootDirectory->readFile($this->rootDirectory->getRelativePath($sourcePath));
        $params = $publisherFile->getViewParams();
        $asset = $this->viewService->createAsset($filePath, $params);

        /**
         * Don't replace anything actually, but publish and preprocess related files
         *
         * @bug: some files end up "published" as DIRECTORIES (but with the file names)
         *
         * @param string $path
         * @return string
         */
        $callback = function($path) use ($asset, $filePath, $params) {
            $this->getRelatedViewFilePath($path, $filePath, $params);
            return $path;
        };

        try {
            /**
             * The CSS content already has relative URLs pre-processed (module notation converted into relative URLs)
             * This hack is needed only to support legacy implementation of LESS preprocessor that relies on publication
             */
            $this->cssUrlResolver->replaceRelativeUrls($content, $callback);
        } catch (\Magento\Exception $e) {
            $this->logger->logException($e);
        }

        $tmpFilePath = \Magento\View\Service::TMP_MATERIALIZATION_DIR . '/' . self::TMP_RESOLVER_DIR . '/'
            . $publisherFile->buildUniquePath();
        $targetDirectory->writeFile($tmpFilePath, $content);

        $processedFile = $this->fileFactory->create(
            $publisherFile->getFilePath(),
            $params,
            $targetDirectory->getAbsolutePath($tmpFilePath)
        );

        return $processedFile;
    }

    /**
     * Get path to file identified by $fileId basing on information about parent file path and name.
     *
     * @param string $fileId URL to the file that was extracted from $parentFilePath
     * @param string $parentFileName original file name identifier that was requested for processing
     * @param array $params theme/module parameters array
     * @return string
     */
    protected function getRelatedViewFilePath($fileId, $parentFileName, $params)
    {
        $relativeFilePath = $this->relatedFile->buildPath($fileId, $parentFileName, $params);
        return $this->fileResolver->getPublicViewFile($relativeFilePath, $params);
    }
}
