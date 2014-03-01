<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs\Config\File\Manager;

/**
 * Config file manager that create config file only if it doesn't exist. Otherwise use existing file
 */
class Caching implements \Magento\RequireJs\Config\File\ManagerInterface
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
     * @var Refresh
     */
    private $refreshStrategy;

    /**
     * @param \Magento\RequireJs\Config $config
     * @param \Magento\App\Filesystem $appFilesystem
     * @param Refresh $refreshStrategy
     */
    public function __construct(
        \Magento\RequireJs\Config $config,
        \Magento\App\Filesystem $appFilesystem,
        Refresh $refreshStrategy
    ) {
        $this->config = $config;
        $this->appFilesystem = $appFilesystem;
        $this->refreshStrategy = $refreshStrategy;
    }

    /**
     * Get absolute path to RequireJs config file. If file doesn't exist, create it
     *
     * @return string
     */
    public function getConfigFile()
    {
        $relPath = $this->config->getConfigFileRelativePath();
        $viewDir = $this->appFilesystem->getDirectoryRead(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        if (!$viewDir->isExist($relPath)) {
            $relPath = $this->refreshStrategy->getConfigFile();
        }
        return $relPath;
    }
}
