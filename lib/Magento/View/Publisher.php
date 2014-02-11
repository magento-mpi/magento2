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
     * @param \Magento\View\Asset\PreProcessor\PreProcessorInterface $preProcessor
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

        // 1. Fallback look-up for view files. Remember it can be file of any type: CSS, LESS, JS, image
        $publisherFile->setSourcePath(
            $this->viewFileSystem->getViewFile(
                $publisherFile->getFilePath(),
                $publisherFile->getViewParams()
            )
        );

        // 2. If $sourcePath returned actually not exists replace it with null value.
        if (!$this->rootDirectory->isExist($this->rootDirectory->getRelativePath($publisherFile->getSourcePath()))) {
            $publisherFile->setSourcePath(null);
        }

        /**
         * 3. Target directory to save temporary files in. It was 'pub/static' dir, but I guess it's more correct
         * to have it in 'var/tmp' dir.
         */
        //TODO: Why should publisher control where pre-processors save temporary files
        $targetDirectory = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);

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

        $this->publishFile($publisherFile);
        return $publisherFile->buildPublicViewFilename();
    }

    /**
     * Publish file
     *
     * @param Publisher\FileInterface $publisherFile
     * @return $this
     */
    protected function publishFile(Publisher\FileInterface $publisherFile)
    {
        $sourcePath = $this->viewFileSystem->normalizePath($publisherFile->getSourcePath());
        $targetPath = $publisherFile->buildPublicViewFilename();

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
