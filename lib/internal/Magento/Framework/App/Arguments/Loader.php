<?php
/**
 * Local Application configuration loader (app/etc/config.php)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Arguments;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\App\DeploymentConfig\BackendConfig;
use Magento\Framework\App\DeploymentConfig\EncryptConfig;
use Magento\Framework\App\DeploymentConfig\DbConfig;
use Magento\Framework\App\DeploymentConfig\SessionConfig;
use Magento\Framework\App\DeploymentConfig\InstallConfig;
use Magento\Framework\App\DeploymentConfig\ResourceConfig;

class Loader
{
    /**
     * Local configuration file
     */
    const PARAM_CUSTOM_FILE = 'custom.options.file';

    /**
     * Deployment config
     *
     * @var \Magento\Framework\App\DeploymentConfig
     */
    private $config;

    /**
     * @param \Magento\Framework\App\Filesystem\DirectoryList $dirList
     * @param string $customFile
     */
    public function __construct(\Magento\Framework\App\Filesystem\DirectoryList $dirList, $customFile = null)
    {
        $configReader = new Reader($dirList, $customFile);
        $this->config = new DeploymentConfig($configReader);
    }

    /**
     * Load configuration
     *
     * @return array
     */
    public function load()
    {
        $result = $this->config->get();
        return !empty($result) ? $result : array();
    }
}
