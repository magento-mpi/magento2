<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\RequireJs;

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
    const FULL_CONFIG_TEMPLATE = <<<config
(function(require){
%base%
%function%

%usages%
})(require);
config;

    /**
     * Template for wrapped partial config
     */
    const PARTIAL_CONFIG_TEMPLATE = <<<config
(function() {
%config%
require.config(config);
})();

config;


    /**
     * @var \Magento\Framework\RequireJs\Config\File\Collector\Aggregated
     */
    private $fileSource;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    private $design;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    private $baseDir;

    /**
     * @var \Magento\Framework\View\Asset\ContextInterface
     */
    private $staticContext;

    /**
     * @param \Magento\Framework\RequireJs\Config\File\Collector\Aggregated $fileSource
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Framework\App\Filesystem $appFilesystem
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     */
    public function __construct(
        \Magento\Framework\RequireJs\Config\File\Collector\Aggregated $fileSource,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\App\Filesystem $appFilesystem,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->fileSource = $fileSource;
        $this->design = $design;
        $this->baseDir = $appFilesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::ROOT_DIR);
        $this->staticContext = $assetRepo->getStaticViewFileContext();
    }

    /**
     * Get aggregated distributed configuration
     *
     * @return string
     */
    public function getConfig()
    {
        $distributedConfig = '';
        $baseConfig = $this->getBaseConfig();
        $customConfigFiles = $this->fileSource->getFiles($this->design->getDesignTheme(), self::CONFIG_FILE_NAME);
        foreach ($customConfigFiles as $file) {
            $config = $this->baseDir->readFile($this->baseDir->getRelativePath($file->getFilename()));
            $distributedConfig .= str_replace(
                array('%config%', '%context%'),
                array($config, $file->getModule()),
                self::PARTIAL_CONFIG_TEMPLATE
            );
        }

        $fullConfig = str_replace(
            array('%function%', '%base%', '%usages%'),
            array($distributedConfig, $baseConfig),
            self::FULL_CONFIG_TEMPLATE
        );

        return $fullConfig;
    }

    /**
     * Get path to config file relative to directory, where all config files with different context are located
     *
     * @return string
     */
    public function getConfigFileRelativePath()
    {
        return self::DIR_NAME . '/' . $this->staticContext->getPath() . '/' . self::CONFIG_FILE_NAME;
    }

    /**
     * Get base RequireJs configuration necessary for working with Magento application
     *
     * @return string
     */
    public function getBaseConfig()
    {
        $config = array(
            'baseUrl' => $this->staticContext->getBaseUrl() . $this->staticContext->getPath()
        );
        $config = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return "require.config($config);\n";
    }
}
