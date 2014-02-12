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
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var WriteInterface
     */
    protected $rootDirectory;

    /**
     * @var \Magento\View\Asset\PreProcessor\PreProcessorInterface
     */
    protected $preProcessor;

    /**
     * @var Publisher\FileFactory
     */
    protected $fileFactory;

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
        $this->filesystem = $filesystem;
        $this->rootDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $this->viewFileSystem = $viewFileSystem;
        $this->preProcessor = $preProcessor;
        $this->fileFactory = $fileFactory;
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
        return $this->getPublishedFilePath($this->fileFactory->create($filePath, $params));
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
     * @return string|null
     * @throws \Magento\Exception
     */
    protected function getPublishedFilePath(Publisher\FileInterface $publisherFile)
    {
        /** If $filePath points to file with protected extension - no publishing, return null */
        if (!$this->isAllowedExtension($publisherFile->getExtension())) {
            return null;
        }

        /**
         * 1. Target directory to save temporary files in. It was 'pub/static' dir, but I guess it's more correct
         * to have it in 'var/tmp' dir.
         */
        //TODO: Why should publisher control where pre-processors save temporary files
        $targetDirectory = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);

        /**
         * 2. Execute asset pre-processors
         */
        $fileToPublish = $this->preProcessor->process($publisherFile, $targetDirectory);

        // 3. If $sourcePath returned still doesn't exists throw Exception
        if (null === $fileToPublish->getSourcePath()
            || !$this->rootDirectory->isExist($this->rootDirectory->getRelativePath($fileToPublish->getSourcePath()))
        ) {
            throw new \Magento\Exception("Unable to locate theme file '{$fileToPublish->getFilePath()}'.");
        }

        /**
         * 4.
         * If $sourcePath points to file in 'pub/lib' dir - no publishing required
         * If $sourcePath points to file in 'pub/static' dir - no publishing required
         * If $sourcePath points to CSS file and developer mode is enabled - publish file
         */
        if (!$fileToPublish->isPublicationAllowed()) {
            return $fileToPublish->getSourcePath();
        }

        $this->publishFile($fileToPublish);
        return $fileToPublish->buildPublicViewFilename();
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
        $targetPath = $publisherFile->buildPublicViewFilename();
        //TODO: we get absolute path and then make relative out of it to use with $targetDirectory

        $targetDirectory = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        $sourcePathRelative = $this->rootDirectory->getRelativePath($sourcePath);
        $targetPathRelative = $targetDirectory->getRelativePath($targetPath);

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
