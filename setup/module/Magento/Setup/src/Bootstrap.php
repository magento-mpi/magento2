<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Bootstrap as AppBootstrap;
use Magento\Framework\App\Filesystem\DirectoryList;
use Zend\Mvc\Application;

/**
 * A bootstrap of Magento Setup application
 */
class Bootstrap
{
    /**
     * Key for default root directory configuration value
     */
    const DEFAULT_ROOT_DIR = 'magento_default_root_dir';

    /**
     * Bootstrap configuration
     *
     * @var array
     */
    private $config;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function initialize(Application $application)
    {
        $directoryList = $this->createDirectoryList($application->getConfig());
        $serviceManager = $application->getServiceManager();
        $serviceManager->setService('Magento\Framework\App\Filesystem\DirectoryList', $directoryList);
        $serviceManager->setService('Magento\Framework\Filesystem', $this->createFilesystem($directoryList));
    }

    /**
     * Initializes DirectoryList service
     *
     * @param array|\ArrayAccess $appConfig
     * @return DirectoryList
     * @throws \LogicException
     */
    public function createDirectoryList($appConfig)
    {
        $paths = [];
        if (isset($this->config[AppBootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS])) {
            $paths = $this->config[AppBootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS];
        }
        if (isset($paths[DirectoryList::ROOT][DirectoryList::PATH])) {
            $rootDir = $paths[DirectoryList::ROOT][DirectoryList::PATH];
        } else {
            if (empty($appConfig[self::DEFAULT_ROOT_DIR])) {
                throw new \LogicException('No default Magento root directory provided.');
            }
            $rootDir = realpath($appConfig[self::DEFAULT_ROOT_DIR]);
        }
        return new DirectoryList($rootDir, $paths);
    }

    /**
     * Initializes Filesystem service
     *
     * @param DirectoryList $directoryList
     * @return Filesystem
     */
    public function createFilesystem(DirectoryList $directoryList)
    {
        $driverPool = new Filesystem\DriverPool;
        return new Filesystem(
            $directoryList,
            new Filesystem\Directory\ReadFactory($driverPool),
            new Filesystem\Directory\WriteFactory($driverPool)
        );
    }
}
