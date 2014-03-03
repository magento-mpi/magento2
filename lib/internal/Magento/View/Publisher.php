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
class Publisher implements FilesManagerInterface
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

    /**
     * View file system
     *
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * Pre-processor
     *
     * @var \Magento\View\Asset\PreProcessor\PreProcessorInterface
     */
    protected $preProcessor;

    /**
     * Publisher file factory
     *
     * @var Publisher\FileFactory
     */
    protected $fileFactory;

    /**
     * Root directory
     *
     * @var WriteInterface
     */
    protected $rootDirectory;

    /**
     * Pre-processors temporary directory
     *
     * @var WriteInterface
     */
    protected $tmpDirectory;

    /**
     * Public directory
     *
     * @var Service
     */
    protected $viewService;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param FileSystem $viewFileSystem
     * @param Asset\PreProcessor\PreProcessorInterface $preProcessor
     * @param Publisher\FileFactory $fileFactory
     * @param Service $viewService
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\View\Asset\PreProcessor\PreProcessorInterface $preProcessor,
        Publisher\FileFactory $fileFactory,
        Service $viewService
    ) {
        $this->rootDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $this->tmpDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);
        $this->viewFileSystem = $viewFileSystem;
        $this->preProcessor = $preProcessor;
        $this->fileFactory = $fileFactory;
        $this->viewService = $viewService;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicViewFile($filePath, array $params)
    {
        $sourceFile = $this->getPreProcessedFileObject($filePath, $params);

        if (!$sourceFile->isPublicationAllowed()) {
            return $sourceFile->getSourcePath();
        }

        $this->publishFile($sourceFile);
        return $sourceFile->buildPublicViewFilename();
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFile($file, array $params = array())
    {
        $readyFile = $this->getPreProcessedFileObject($file, $params);
        return $readyFile->getSourcePath();
    }

    /**
     * Find source file and return publisher container for it
     *
     * @param string $file
     * @param array $params
     * @return Publisher\FileInterface
     * @throws \Magento\Exception
     */
    protected function getPreProcessedFileObject($file, array $params = array())
    {
        $fileContainer = $this->fileFactory->create($file, $params);

        $extension = $fileContainer->getExtension();
        if (!$this->isAllowedExtension($extension)) {
            throw new \Magento\Exception("Files with extension '{$extension}' may not be published.");
        }

        $readyFile = $this->preProcessor->process($fileContainer, $this->tmpDirectory);

        if (!$readyFile->isSourceFileExists()) {
            throw new \Magento\Exception("Unable to locate theme file '{$readyFile->getFilePath()}'.");
        }

        return $readyFile;
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
     * Publish file
     *
     * @param Publisher\FileInterface $publisherFile
     */
    protected function publishFile(Publisher\FileInterface $publisherFile)
    {
        $asset = $this->viewService->createAsset($publisherFile->getFilePath(), $publisherFile->getViewParams());
        $this->viewService->publishAsset($asset);
        $this->viewFileSystem->notifyViewFileLocationChanged($publisherFile);
    }
}
