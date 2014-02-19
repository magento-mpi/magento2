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
     * @var \Magento\View\Url
     */
    protected $viewUrl;

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
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\RelatedFile $relatedFile
     * @param \Magento\View\Url\CssResolver $cssUrlResolver
     * @param \Magento\View\Url $viewUrl
     * @param \Magento\Logger $logger
     * @param \Magento\View\Publisher\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\RelatedFile $relatedFile,
        \Magento\View\Url\CssResolver $cssUrlResolver,
        \Magento\View\Url $viewUrl,
        \Magento\Logger $logger,
        \Magento\View\Publisher\FileFactory $fileFactory
    ) {
        $this->rootDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $this->relatedFile = $relatedFile;
        $this->cssUrlResolver = $cssUrlResolver;
        $this->viewUrl = $viewUrl;
        $this->logger = $logger;
        $this->fileFactory = $fileFactory;
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

        $callback = function ($fileId) use ($filePath, $params) {
            $relatedPathPublic = $this->publishRelatedViewFile($fileId, $filePath, $params);
            return $relatedPathPublic;
        };
        try {
            $content = $this->cssUrlResolver->replaceCssRelativeUrls(
                $content,
                $sourcePath,
                $publisherFile->buildPublicViewFilename(),
                $callback
            );
        } catch (\Magento\Exception $e) {
            $this->logger->logException($e);
        }

        $tmpFilePath = Composite::TMP_VIEW_DIR . '/' . self::TMP_RESOLVER_DIR . '/'
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
        return $this->viewUrl->getViewFileUrl($relativeFilePath, $params);
    }
}
