<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs;

/**
 * Class responsible for preparing and providing RequireJs config
 */
class Config
{
    /**
     * Name of sub-directory where generated RequireJs config is placed
     */
    const DIR_NAME = '_requirejs';

    /**
     * File name of RequireJs config
     */
    const CONFIG_FILE_NAME = 'requirejs-config.js';

    /**
     * @var File\Source\Aggregated
     */
    private $fileSource;

    /**
     * @var \Magento\View\DesignInterface
     */
    private $design;

    /**
     * @var \Magento\App\Filesystem
     */
    private $appFilesystem;

    /**
     * @var \Magento\View\Path
     */
    private $path;

    /**
     * @var \Magento\UrlInterface
     */
    private $baseUrl;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    private $baseDir;

    public function __construct(
        File\Source\Aggregated $fileSource,
        \Magento\View\DesignInterface $design,
        \Magento\App\Filesystem $appFilesystem,
        \Magento\View\Path $path,
        \Magento\UrlInterface $baseUrl
    ) {
        $this->fileSource = $fileSource;
        $this->design = $design;
        $this->appFilesystem = $appFilesystem;
        $this->path = $path;
        $this->baseUrl = $baseUrl;
        $this->baseDir = $this->appFilesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
    }

    /**
     * Get declaration of JS function that updates paths basing on module context
     *
     * @return string
     */
    public function getPathsUpdaterJs()
    {
        $functionSource = __DIR__ . '/paths-updater.js';
        return $this->baseDir->readFile($this->baseDir->getRelativePath($functionSource));
    }

    /**
     * Get aggregated distributed configuration
     *
     * @return string
     */
    public function getConfig()
    {
        $fullConfig = '';
        $customConfigFiles = $this->fileSource->getFiles($this->design->getDesignTheme(), self::CONFIG_FILE_NAME);
        foreach ($customConfigFiles as $file) {
            $config = $this->baseDir->readFile($this->baseDir->getRelativePath($file->getFilename()));
            $fullConfig .= $this->wrapConfig($config, $file->getModule());
        }

        return $fullConfig;
    }

    /**
     * Save configuration into public file
     *
     * @param string $content
     */
    public function crateConfigFile($content)
    {
        $relPath = self::DIR_NAME . '/' . $this->getContextPath() . '/' . self::CONFIG_FILE_NAME;

        // todo: check existence depending on application mode

        $viewDir = $this->appFilesystem->getDirectoryWrite(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        $viewDir->writeFile($relPath, $content);
    }

    /**
     * Wrap config object with paths updater call
     *
     * @param string $config
     * @param string $moduleContext
     * @return string
     */
    protected function wrapConfig($config, $moduleContext = '')
    {
        $config = trim($config);
        if ($moduleContext) {
            $moduleContext = ", '$moduleContext'";
        }
        return "mageConfigRequireJs({$config}{$moduleContext});" . PHP_EOL;
    }

    /**
     * Get configuration for baseUrl
     *
     * @return string
     */
    public function getBaseUrlConfig()
    {
        $config = array(
            'baseUrl' => $this->getBaseUrl() . $this->getContextPath(),
        );
        $config = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return "require.config($config);" . PHP_EOL;
    }

    /**
     * Get URL for the config
     *
     * @return string
     */
    public function getConfigUrl()
    {
        // todo: check signing
        return $this->getBaseUrl() . self::DIR_NAME . '/' . $this->getContextPath() . '/' . self::CONFIG_FILE_NAME;
    }

    /**
     * Get base URL to view dir
     *
     * @return string
     */
    protected function getBaseUrl()
    {
        return $this->baseUrl->getBaseUrl(array('_type' => \Magento\UrlInterface::URL_TYPE_STATIC));
    }

    /**
     * Get part of path specifying current context (area, theme, etc.)
     *
     * @return string
     */
    protected function getContextPath()
    {
        return $this->path->getRelativePath(
            $this->design->getArea(),
            $this->design->getDesignTheme(),
            $this->design->getLocale()
        );
    }
}
