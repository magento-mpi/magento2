<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

use Magento\Filesystem\Directory\WriteInterface;

/**
 * Magento view file publisher
 */
class Publisher implements \Magento\View\PublicFilesManagerInterface
{
    /**#@+
     * Extensions group for static files
     */
    const CONTENT_TYPE_CSS = 'css';
    const CONTENT_TYPE_JS  = 'js';
    /**#@-*/

    /**#@+
     * Protected extensions group for publication mechanism
     */
    const CONTENT_TYPE_PHP   = 'php';
    const CONTENT_TYPE_PHTML = 'phtml';
    const CONTENT_TYPE_XML   = 'xml';
    /**#@-*/

    /**#@+
     * Public directories prefix group
     */
    const PUBLIC_MODULE_DIR = '_module';
    const PUBLIC_VIEW_DIR   = '_view';
    const PUBLIC_THEME_DIR  = '_theme';
    /**#@-*/

    /**
     * @var \Magento\App\Filesystem
     */
    protected $_filesystem;

    /**
     * Helper to process css content
     *
     * @var \Magento\View\Url\CssResolver
     */
    protected $_cssUrlResolver;

    /**
     * @var \Magento\View\Service
     */
    protected $_viewService;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $_viewFileSystem;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var WriteInterface
     */
    protected $rootDirectory;

    /**
     * @var RelatedFile
     */
    protected $relatedFile;

    /**
     * @var \Magento\View\Asset\PreProcessor\PreProcessorInterface
     */
    protected $preProcessor;

    /**
     * @var Publisher\FileFactory
     */
    protected $fileFactory;

    /**
     * @var Publisher\PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\Url\CssResolver $cssUrlResolver
     * @param Service $viewService
     * @param FileSystem $viewFileSystem
     * @param RelatedFile $relatedFile
     * @param \Magento\View\Asset\PreProcessor\PreProcessorInterface $preProcessor
     * @param Publisher\FileFactory $fileFactory
     * @param Publisher\PathBuilderInterface $pathBuilder
     * @param bool $allowDuplication
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\App\Filesystem $filesystem,
        \Magento\View\Url\CssResolver $cssUrlResolver,
        \Magento\View\Service $viewService,
        \Magento\View\FileSystem $viewFileSystem,
        RelatedFile $relatedFile,
        \Magento\View\Asset\PreProcessor\PreProcessorInterface $preProcessor,
        Publisher\FileFactory $fileFactory,
        Publisher\PathBuilderInterface $pathBuilder,
        $allowDuplication
    ) {
        $this->_filesystem = $filesystem;
        $this->rootDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $this->_cssUrlResolver = $cssUrlResolver;
        $this->_viewService = $viewService;
        $this->_viewFileSystem = $viewFileSystem;
        $this->_logger = $logger;
        $this->_allowDuplication = $allowDuplication;
        $this->relatedFile = $relatedFile;
        $this->preProcessor = $preProcessor;
        $this->fileFactory = $fileFactory;
        $this->pathBuilder = $pathBuilder;
    }

    /**
     * Get published file path
     *
     * @param  string $filePath
     * @param  array $params
     * @return string
     */
    public function getPublicFilePath($filePath, $params)
    {
        return $this->_getPublishedFilePath($this->fileFactory->create($filePath, $params));
    }

    /**
     * Publish file identified by $fileId basing on information about parent file path and name.
     *
     * @param string $fileId URL to the file that was extracted from $parentFilePath
     * @param string $parentFilePath path to the file
     * @param string $parentFileName original file name identifier that was requested for processing
     * @param array $params theme/module parameters array
     * @return string
     */
    protected function _publishRelatedViewFile($fileId, $parentFilePath, $parentFileName, $params)
    {
        $relativeFilePath = $this->relatedFile->buildPath($fileId, $parentFilePath, $parentFileName, $params);
        return $this->_getPublishedFilePath($this->fileFactory->create($relativeFilePath, $params));
    }

    /**
     * @param string $extension
     * @return bool
     */
    protected function isAllowedExtension($extension)
    {
        $protectedExtensions = array(
            self::CONTENT_TYPE_PHP,
            self::CONTENT_TYPE_PHTML,
            self::CONTENT_TYPE_XML
        );
        if (in_array($extension, $protectedExtensions)) {
            return false;
        }
        return true;
    }

    /**
     * Get published file path
     *
     * Check, if requested theme file has public access, and move it to public folder, if the file has no public access
     *
     * @param Publisher\FileInterface $publisherFile
     * @throws \Magento\Exception
     * @return string|null
     */
    protected function _getPublishedFilePath(Publisher\FileInterface $publisherFile)
    {
        //TODO: Do we need this? It throws exception in production mode!
        if (!$this->_viewService->isViewFileOperationAllowed()) {
            throw new \Magento\Exception('Filesystem operations are not permitted for view files');
        }

        /** If $filePath points to file with protected extension - no publishing, return null */
        if (!$this->isAllowedExtension($publisherFile->getExtension())) {
            return null;
        }

        // 1. Fallback look-up for view files. Remember it can be file of any type: CSS, LESS, JS, image
        $publisherFile->setSourcePath(
            $this->_viewFileSystem->getViewFile(
                $publisherFile->getFilePath(),
                $publisherFile->getViewParams()
            )
        );

        // 2. If $sourcePath returned actually not exists replace it with null value.
        if (!$this->rootDirectory->isExist($this->rootDirectory->getRelativePath($publisherFile->getSourcePath()))) {
            return null;
        }

        /**
         * 3. Target directory to save temporary files in. It was 'pub/static' dir, but I guess it's more correct
         * to have it in 'var/tmp' dir.
         */
        //TODO: Why should publisher control where pre-processors save temporary files
        $targetDirectory = $this->_filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);

        /**
         * 4. Execute asset pre-processors
         *      in case if $sourcePath was null, then pre-processors will be executed and original source file
         *          will be processed, then new $sourcePath targeting pre-processed file in 'var/tmp' dir
         *          will be returned back
         *      in case if $sourcePath was not null then $sourcePath passed will be returned back
         */
        $publisherFile->setSourcePath($this->preProcessor->process($publisherFile, $targetDirectory));

        // 5. If $sourcePath returned still doesn't exists throw Exception
        if (null === $publisherFile->getSourcePath()
            || !$this->rootDirectory->isExist($this->rootDirectory->getRelativePath($publisherFile->getSourcePath()))
        ) {
            throw new \Magento\Exception("Unable to locate theme file '{$publisherFile->getFilePath()}'.");
        }

        /**
         * 6.
         * If $sourcePath points to file in 'pub/lib' dir - no publishing required
         * If $sourcePath points to file in 'pub/static' dir - no publishing required
         * If $sourcePath points to CSS file and developer mode is enabled - publish file
         */
        if (!$publisherFile->isPublicationAllowed()) {
            return $publisherFile->getSourcePath();
        }

        return $this->_publishFile($publisherFile);
    }

    /**
     * Publish file
     *
     * @param Publisher\FileInterface $publisherFile
     * @return string
     */
    protected function _publishFile(Publisher\FileInterface $publisherFile)
    {
        $filePath = $this->_viewFileSystem->normalizePath($publisherFile->getFilePath());
        $sourcePath = $this->_viewFileSystem->normalizePath($publisherFile->getSourcePath());
        $targetPath = $this->pathBuilder->buildPublishedFilePath($publisherFile);

        $targetDirectory = $this->_filesystem->getDirectoryWrite(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        $sourcePathRelative = $this->rootDirectory->getRelativePath($sourcePath);
        $targetPathRelative = $targetDirectory->getRelativePath($targetPath);

        if ($publisherFile->getExtension() === self::CONTENT_TYPE_CSS) {
            $cssContent = $this->_getPublicCssContent(
                $sourcePath,
                $targetPath,
                $filePath,
                $publisherFile->getViewParams()
            );
        }

        $fileMTime = $this->rootDirectory->stat($sourcePathRelative)['mtime'];

        if (!$targetDirectory->isExist($targetPathRelative)
            || $fileMTime != $targetDirectory->stat($targetPathRelative)['mtime']
        ) {
            if (isset($cssContent)) {
                $targetDirectory->writeFile($targetPathRelative, $cssContent);
                $targetDirectory->touch($targetPathRelative, $fileMTime);
            } elseif ($this->rootDirectory->isFile($sourcePathRelative)) {
                $this->rootDirectory->copyFile($sourcePathRelative, $targetPathRelative, $targetDirectory);
                $targetDirectory->touch($targetPathRelative, $fileMTime);
            }
        }

        $this->_viewFileSystem->notifyViewFileLocationChanged($targetPath, $filePath, $publisherFile->getViewParams());
        return $targetPath;
    }

    /**
     * Retrieve processed CSS file content that contains URLs relative to the specified public directory
     *
     * @param string $sourcePath Absolute path to the current location of CSS file
     * @param string $publicPath Absolute path to location of the CSS file, where it will be published
     * @param string $fileName File name used for reference
     * @param array $params Design parameters
     * @return string
     */
    protected function _getPublicCssContent($sourcePath, $publicPath, $fileName, $params)
    {
        $content = $this->rootDirectory->readFile($this->rootDirectory->getRelativePath($sourcePath));

        $callback = function ($fileId, $originalPath) use ($fileName, $params) {
            $relatedPathPublic = $this->_publishRelatedViewFile(
                $fileId,
                $originalPath,
                $fileName,
                $params
            );
            return $relatedPathPublic;
        };
        try {
            $content = $this->_cssUrlResolver->replaceCssRelativeUrls($content, $sourcePath, $publicPath, $callback);
        } catch (\Magento\Exception $e) {
            $this->_logger->logException($e);
        }
        return $content;
    }
}
