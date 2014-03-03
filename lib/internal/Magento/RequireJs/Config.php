<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs;

/**
 * Provider of RequireJs config information
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
     * Template for combined RequireJs config file
     */
    const CONFIG_TEMPLATE = <<<DOD
(function(require){
%function%

%usages%
})(require);
DOD;

    /**
     * @var \Magento\RequireJs\Config\File\Source\Aggregated
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
        Config\File\Source\Aggregated $fileSource,
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
     * Get aggregated distributed configuration
     *
     * @return string
     */
    public function getConfig()
    {
        $functionSource = __DIR__ . '/paths-updater.js';
        $functionDeclaration = $this->baseDir->readFile($this->baseDir->getRelativePath($functionSource));

        $distributedConfig = '';
        $customConfigFiles = $this->fileSource->getFiles($this->design->getDesignTheme(), self::CONFIG_FILE_NAME);
        foreach ($customConfigFiles as $file) {
            $config = $this->baseDir->readFile($this->baseDir->getRelativePath($file->getFilename()));
            $distributedConfig .= $this->wrapConfig($config, $file->getModule());
        }

        $fullConfig = str_replace(
            array('%function%', '%usages%'),
            array($functionDeclaration, $distributedConfig),
            self::CONFIG_TEMPLATE
        );

        return $fullConfig;
    }

    /**
     * Get path to config file relative to directory for, where all config files with different context are located
     *
     * @return string
     */
    public function getConfigFileRelativePath()
    {
        return self::DIR_NAME . '/' . $this->getContextPath() . '/' . self::CONFIG_FILE_NAME;
    }

    /**
     * Get base RequireJs configuration necessary for working with Magento application
     *
     * @return string
     */
    public function getBaseConfig()
    {
        $config = array(
            'baseUrl' => $this->getBaseUrl() . $this->getContextPath(),
            'paths' => array(
                'magento' => 'mage/requirejs/plugin/id-normalizer',
            ),
        );
        $config = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return "require.config($config);\n";
    }

    /**
     * Get URL for the config
     *
     * @return string
     */
    public function getConfigUrl()
    {
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
        return "mageConfigRequireJs({$config}{$moduleContext});\n";
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
