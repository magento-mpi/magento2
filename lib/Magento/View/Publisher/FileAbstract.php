<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Publisher;

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
     * @var null|string
     */
    protected $sourcePath;

    /**
     * Indicates how to materialize view files: with or without "duplication"
     *
     * @var bool
     */
    protected $allowDuplication;

    /**
     * @var bool
     */
    protected $isPublicationAllowed;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\Service $viewService
     * @param \Magento\Module\Dir\Reader $modulesReader
     * @param string $filePath
     * @param string $extension
     * @param bool $allowDuplication
     * @param array $viewParams
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\Service $viewService,
        \Magento\Module\Dir\Reader $modulesReader,
        $filePath,
        $extension,
        $allowDuplication,
        array $viewParams
    ) {
        $this->filesystem = $filesystem;
        $this->viewService = $viewService;
        $this->modulesReader = $modulesReader;
        $this->filePath = $filePath;
        $this->extension = $extension;
        $this->allowDuplication = $allowDuplication;
        $this->viewParams = $viewParams;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Original file extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return array
     */
    public function getViewParams()
    {
        return $this->viewParams;
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

    /**
     * @param string $sourcePath
     * @return $this
     */
    public function setSourcePath($sourcePath)
    {
        $this->sourcePath = $sourcePath;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSourcePath()
    {
        return $this->sourcePath;
    }

    /**
     * Build unique file path for publication
     *
     * @return string
     */
    abstract public function getPublicationPath();

    /**
     * Build public filename for a theme file that always includes area/package/theme/locate parameters
     *
     * @return string
     */
    protected function buildPublicViewRedundantFilename()
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
     * Build public filename for a view file that sufficiently depends on the passed parameters
     *
     * @return string
     */
    protected function buildPublicViewSufficientFilename()
    {
        $designDir = $this->filesystem->getPath(\Magento\App\Filesystem::THEMES_DIR) . '/';
        if (0 === strpos($this->getSourcePath(), $designDir)) {
            // theme file
            $publicFile = substr($this->getSourcePath(), strlen($designDir));
        } else {
            // modular file
            $module = $this->getViewParams()['module'];
            $moduleDir = $this->modulesReader->getModuleDir('theme', $module) . '/';
            $publicFile = substr($this->getSourcePath(), strlen($moduleDir));
            $publicFile = self::PUBLIC_MODULE_DIR . '/' . $module . '/' . $publicFile;
        }
        return $publicFile;
    }

    /**
     * Build path to file located in public folder
     *
     * @return string
     */
    public function buildPublicViewFilename()
    {
        return $this->viewService->getPublicDir() . '/' . $this->getPublicationPath();
    }
}
