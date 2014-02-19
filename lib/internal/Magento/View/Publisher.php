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
class Publisher implements PublicFilesManagerInterface
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
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var \Magento\View\Asset\PreProcessor\PreProcessorInterface
     */
    protected $preProcessor;

    /**
     * @var Publisher\FileFactory
     */
    protected $fileFactory;

    /**
     * @var WriteInterface
     */
    protected $rootDirectory;

    /**
     * @var WriteInterface
     */
    protected $tmpDirectory;

    /**
     * @var WriteInterface
     */
    protected $pubDirectory;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param FileSystem $viewFileSystem
     * @param Asset\PreProcessor\PreProcessorInterface $preProcessor
     * @param Publisher\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\View\Asset\PreProcessor\PreProcessorInterface $preProcessor,
        Publisher\FileFactory $fileFactory
    ) {
        $this->rootDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $this->tmpDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);
        $this->pubDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        $this->viewFileSystem = $viewFileSystem;
        $this->preProcessor = $preProcessor;
        $this->fileFactory = $fileFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicViewFile($filePath, array $params)
    {
        return $this->getPublishedFilePath($filePath, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFile($file, array $params = array())
    {
        $readyFile = $this->getSourceFileContainer($file, $params);
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
    protected function getSourceFileContainer($file, array $params = array())
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
     * Get published file path
     *
     * Check, if requested theme file has public access, and move it to public folder, if the file has no public access
     *
     * @param string $file
     * @param array $params
     * @throws \Magento\Exception
     * @return string|null
     */
    protected function getPublishedFilePath($file, $params)
    {
        $sourceFile = $this->getSourceFileContainer($file, $params);

        if (!$sourceFile->isPublicationAllowed()) {
            return $sourceFile->getSourcePath();
        }

        $this->publishFile($sourceFile);
        return $sourceFile->buildPublicViewFilename();
    }

    /**
     * Publish file
     *
     * @param Publisher\FileInterface $publisherFile
     * @return $this
     */
    protected function publishFile(Publisher\FileInterface $publisherFile)
    {
        $sourcePath = $publisherFile->getSourcePath();
        $sourcePathRelative = $this->rootDirectory->getRelativePath($sourcePath);

        $targetPathRelative = $publisherFile->buildUniquePath();

        $targetDirectory = $this->pubDirectory;

        $fileMTime = $this->rootDirectory->stat($sourcePathRelative)['mtime'];
        if (!$targetDirectory->isExist($targetPathRelative)
            || $fileMTime != $targetDirectory->stat($targetPathRelative)['mtime']
        ) {
            if ($this->rootDirectory->isFile($sourcePathRelative)) {
                $this->rootDirectory->copyFile($sourcePathRelative, $targetPathRelative, $targetDirectory);
                $targetDirectory->touch($targetPathRelative, $fileMTime);
            } elseif (!$targetDirectory->isDirectory($targetPathRelative)) {
                $targetDirectory->create($targetPathRelative);
            }
        }

        $this->viewFileSystem->notifyViewFileLocationChanged($publisherFile);
        return $this;
    }
}
