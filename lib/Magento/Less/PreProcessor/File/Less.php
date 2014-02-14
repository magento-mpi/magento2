<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\PreProcessor\File;

use Magento\View;

/**
 * Less file
 */
class Less
{
    /**
     * Folder for publication preprocessed less files
     */
    const PUBLICATION_PREFIX_PATH = 'less';

    /**
     * @var View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected $viewParams;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var string
     */
    protected $sourcePath;

    /**
     * @var bool
     */
    protected $isPublished = false;

    /**
     * @param View\FileSystem $viewFileSystem
     * @param \Magento\App\Filesystem $filesystem
     * @param string $filePath
     * @param array $viewParams
     * @param string|null $sourcePath
     */
    public function __construct(
        View\FileSystem $viewFileSystem,
        \Magento\App\Filesystem $filesystem,
        $filePath,
        array $viewParams,
        $sourcePath = null
    ) {
        $this->viewFileSystem = $viewFileSystem;
        $this->filesystem = $filesystem;
        $this->filePath = $filePath;
        $this->viewParams = $viewParams;
        $this->sourcePath = $sourcePath ?: $this->getSourcePath();
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return array
     */
    public function getViewParams()
    {
        return $this->viewParams;
    }

    /**
     * Return source path of file if it's exist
     *
     * @return string
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function getSourcePath()
    {
        if ($this->sourcePath === null) {
            $this->sourcePath = $this->viewFileSystem->getViewFile($this->getFilePath(), $this->getViewParams());
            if (!$this->getDirectoryRead()->isExist($this->getDirectoryRead()->getRelativePath($this->sourcePath))) {
                throw new \Magento\Filesystem\FilesystemException("File '{$this->sourcePath}' isn't exist");
            }
        }
        return $this->sourcePath;
    }

    /**
     * Build unique file path for publication
     *
     * @return string
     */
    public function getPublicationPath()
    {
        $sourcePathPrefix = $this->getDirectoryRead()->getAbsolutePath();
        $targetPathPrefix = $this->getDirectoryWrite()->getAbsolutePath() . self::PUBLICATION_PREFIX_PATH . '/';
        return str_replace($sourcePathPrefix, $targetPathPrefix, $this->getSourcePath());
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $directoryRead = $this->getDirectoryRead();
        $filePath = $this->isPublished() ? $this->getPublicationPath() : $this->getSourcePath();
        return $directoryRead->readFile($directoryRead->getRelativePath($filePath));
    }

    /**
     * Save file content to publication path
     *
     * @param string $content
     */
    public function saveContent($content)
    {
        $directoryWrite = $this->getDirectoryWrite();
        $directoryWrite->writeFile($directoryWrite->getRelativePath($this->getPublicationPath()), $content);
        $this->isPublished = true;
    }

    /**
     * Publishing state
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->isPublished;
    }

    /**
     * Unique identifier for a file
     *
     * @return string
     */
    public function getFileIdentifier()
    {
        $themeIdentifier = !empty($this->viewParams['themeModel']) && $this->viewParams['themeModel']->getFullPath()
            ? $this->viewParams['themeModel']->getFullPath()
            : 'base';
        return implode('|', [$this->filePath, $this->viewParams['module'], $themeIdentifier]);
    }

    /**
     * Get base directory with source of less files
     *
     * @return \Magento\Filesystem\Directory\ReadInterface
     */
    public function getDirectoryRead()
    {
        return $this->filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
    }

    /**
     * Get directory for publication temporary less files
     *
     * @return \Magento\Filesystem\Directory\WriteInterface
     */
    public function getDirectoryWrite()
    {
        return $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::TMP_DIR);
    }
}
