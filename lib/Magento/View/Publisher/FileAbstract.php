<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Publisher;

use Magento\Filesystem\Directory\WriteInterface;

/**
 * Abstract publisher file type
 */
abstract class FileAbstract implements FileInterface
{
    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\View\Service
     */
    protected $viewService;

    /**
     * @var \Magento\Module\Dir\Reader
     */
    protected $modulesReader;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var array
     */
    protected $viewParams;

    /**
     * @var string|null
     */
    protected $sourcePath;

    /**
     * @var bool
     */
    protected $isPublicationAllowed;

    /**
     * @var WriteInterface
     */
    protected $rootDirectory;

    /**
     * Makes sure that fallback is only used once per file and only if no 'valid' source path was passed to constructor
     *
     * @var bool
     */
    protected $isFallbackUsed = false;

    /**
     * Makes sure that source path is not overwritten when 'valid' value of source path was passed to constructor
     *
     * @var bool
     */
    protected $isSourcePathProvided;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\Service $viewService
     * @param \Magento\Module\Dir\Reader $modulesReader
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param string $filePath
     * @param array $viewParams
     * @param string|null $sourcePath
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\Service $viewService,
        \Magento\Module\Dir\Reader $modulesReader,
        \Magento\View\FileSystem $viewFileSystem,
        $filePath,
        array $viewParams,
        $sourcePath = null
    ) {
        $this->filesystem = $filesystem;
        $this->viewService = $viewService;
        $this->modulesReader = $modulesReader;
        $this->filePath = $filePath;
        $this->viewParams = $viewParams;
        $this->viewFileSystem = $viewFileSystem;
        $this->rootDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $this->setSourcePath($sourcePath);
        $this->isSourcePathProvided = $sourcePath !== null;
    }

    /**
     * Determine whether a file needs to be published
     *
     * All files located in 'pub/lib' dir should not be published cause it's already publicly accessible.
     * All other files must be processed either if they are not published already (located in 'pub/static'),
     * or if they are css-files and we're working in developer mode.
     *
     * If sourcePath points to file in 'pub/lib' dir - no publishing required
     * If sourcePath points to file in 'pub/static' dir - no publishing required
     *
     * @return bool
     */
    abstract public function isPublicationAllowed();

    /**
     * Build unique file path for publication
     *
     * @return string
     */
    public function buildUniquePath()
    {
        /** @var $theme \Magento\View\Design\ThemeInterface */
        $theme = $this->getViewParams()['themeModel'];
        if ($theme->getThemePath()) {
            $designPath = $theme->getThemePath();
        } elseif ($theme->getId()) {
            $designPath = self::PUBLIC_THEME_DIR . $theme->getId();
        } else {
            $designPath = self::PUBLIC_VIEW_DIR;
        }

        $publicFile = $this->getViewParams()['area'] . '/' . $designPath . '/' . $this->getViewParams()['locale']
            . ($this->getViewParams()['module'] ? '/' . $this->getViewParams()['module'] : '')
            . '/' . $this->getFilePath();

        return $publicFile;
    }

    /**
     * Original file extension
     *
     * @return string
     */
    public function getExtension()
    {
        if ($this->extension === null) {
            $this->extension = strtolower(pathinfo($this->getFilePath(), PATHINFO_EXTENSION));
        }
        return $this->extension;
    }

    /**
     * @return bool
     */
    public function isSourceFileExists()
    {
        return $this->getSourcePath() !== null;
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
     * Build path to file located in public folder
     *
     * @return string
     */
    public function buildPublicViewFilename()
    {
        return $this->viewService->getPublicDir() . '/' . $this->buildUniquePath();
    }

    /**
     * @return string|null
     */
    public function getSourcePath()
    {
        if (!$this->isSourcePathProvided && !$this->isFallbackUsed) {
            $this->isFallbackUsed = false;

            // Fallback look-up for view files. Remember it can be file of any type: CSS, LESS, JS, image
            $fallbackSourcePath = $this->viewFileSystem->getViewFile($this->getFilePath(), $this->getViewParams());
            $this->setSourcePath($fallbackSourcePath);
        }
        return $this->sourcePath;
    }

    /**
     * @param string $sourcePath
     * @return $this
     */
    protected function setSourcePath($sourcePath)
    {
        if ($sourcePath === null
            || !$this->rootDirectory->isExist($this->rootDirectory->getRelativePath($sourcePath))
        ) {
            $this->sourcePath = null;
        } else {
            $this->sourcePath = $sourcePath;
        }

        return $this;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    protected function isLibFile($filePath)
    {
        $pubLibDir = $this->filesystem->getPath(\Magento\App\Filesystem::PUB_LIB_DIR) . '/';
        if (strncmp($filePath, $pubLibDir, strlen($pubLibDir)) === 0) {
            return true;
        }
        return false;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    protected function isViewStaticFile($filePath)
    {
        $pubStaticDir = $this->filesystem->getPath(\Magento\App\Filesystem::STATIC_VIEW_DIR) . '/';
        if (strncmp($filePath, $pubStaticDir, strlen($pubStaticDir)) === 0) {
            return true;
        }
        return false;
    }
}
