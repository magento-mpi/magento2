<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Publisher;

/**
 * Publisher path builder
 */
class PathBuilder implements PathBuilderInterface
{
    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * Helper to process css content
     *
     * @var \Magento\View\Url\CssResolver
     */
    protected $cssUrlResolver;

    /**
     * @var \Magento\View\Service
     */
    protected $viewService;

    /**
     * @var \Magento\Module\Dir\Reader
     */
    protected $modulesReader;

    /**
     * Indicates how to materialize view files: with or without "duplication"
     *
     * @var bool
     */
    protected $allowDuplication;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\View\Url\CssResolver $cssUrlResolver
     * @param \Magento\View\Service $viewService
     * @param \Magento\Module\Dir\Reader $modulesReader
     * @param $allowDuplication
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        \Magento\View\Url\CssResolver $cssUrlResolver,
        \Magento\View\Service $viewService,
        \Magento\Module\Dir\Reader $modulesReader,
        $allowDuplication
    ) {
        $this->filesystem = $filesystem;
        $this->cssUrlResolver = $cssUrlResolver;
        $this->viewService = $viewService;
        $this->modulesReader = $modulesReader;
        $this->allowDuplication = $allowDuplication;
    }

    /**
     * Build published file path
     *
     * @param FileInterface $publisherFile
     * @return string
     */
    public function buildPublishedFilePath(FileInterface $publisherFile)
    {
        $isCssFile = $publisherFile->getExtension() === \Magento\View\Publisher::CONTENT_TYPE_CSS;
        if ($this->allowDuplication || $isCssFile) {
            $targetPath = $this->buildPublicViewRedundantFilename(
                $publisherFile->getFilePath(),
                $publisherFile->getViewParams()
            );
        } else {
            $targetPath = $this->buildPublicViewSufficientFilename(
                $publisherFile->getSourcePath(),
                $publisherFile->getViewParams()
            );
        }
        $targetPath = $this->buildPublicViewFilename($targetPath);

        return $targetPath;
    }

    /**
     * Build public filename for a theme file that always includes area/package/theme/locate parameters
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    protected function buildPublicViewRedundantFilename($file, array $params)
    {
        /** @var $theme \Magento\View\Design\ThemeInterface */
        $theme = $params['themeModel'];
        if ($theme->getThemePath()) {
            $designPath = $theme->getThemePath();
        } elseif ($theme->getId()) {
            $designPath = \Magento\View\Publisher::PUBLIC_THEME_DIR . $theme->getId();
        } else {
            $designPath = \Magento\View\Publisher::PUBLIC_VIEW_DIR;
        }

        $publicFile = $params['area'] . '/' . $designPath . '/' . $params['locale'] .
            ($params['module'] ? '/' . $params['module'] : '') . '/' . $file;

        return $publicFile;
    }

    /**
     * Build public filename for a view file that sufficiently depends on the passed parameters
     *
     * @param string $filename
     * @param array $params
     * @return string
     */
    protected function buildPublicViewSufficientFilename($filename, array $params)
    {
        $designDir = $this->filesystem->getPath(\Magento\App\Filesystem::THEMES_DIR) . '/';
        if (0 === strpos($filename, $designDir)) {
            // theme file
            $publicFile = substr($filename, strlen($designDir));
        } else {
            // modular file
            $module = $params['module'];
            $moduleDir = $this->modulesReader->getModuleDir('theme', $module) . '/';
            $publicFile = substr($filename, strlen($moduleDir));
            $publicFile = \Magento\View\Publisher::PUBLIC_MODULE_DIR . '/' . $module . '/' . $publicFile;
        }
        return $publicFile;
    }

    /**
     * Build path to file located in public folder
     *
     * @param string $file
     * @return string
     */
    protected function buildPublicViewFilename($file)
    {
        return $this->viewService->getPublicDir() . '/' . $file;
    }
}
