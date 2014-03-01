<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs\Config\File\Manager;

/**
 * Config file manager that always refresh the file
 */
class Refresh implements \Magento\RequireJs\Config\File\ManagerInterface
{
    /**
     * @var \Magento\RequireJs\Config
     */
    private $config;

    /**
     * @var \Magento\App\Filesystem
     */
    private $appFilesystem;

    /**
     * @param \Magento\RequireJs\Config $config
     * @param \Magento\App\Filesystem $appFilesystem
     */
    public function __construct(
        \Magento\RequireJs\Config $config,
        \Magento\App\Filesystem $appFilesystem
    ) {
        $this->config = $config;
        $this->appFilesystem = $appFilesystem;
    }

    /**
     * Create file with RequireJs configuration and provide absolute path to it
     *
     * @return string
     */
    public function getConfigFile()
    {
        $relPath = $this->config->getConfigFileRelativePath();
        $content = $this->config->getPathsUpdaterJs() . $this->config->getConfig();

        $viewDir = $this->appFilesystem->getDirectoryWrite(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        $viewDir->writeFile($relPath, $content);
        return $viewDir->getAbsolutePath($relPath);
    }
}
