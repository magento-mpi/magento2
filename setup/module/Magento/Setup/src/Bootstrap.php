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
use Zend\ServiceManager\ServiceManager;

/**
 * A bootstrap of Magento Setup application
 */
class Bootstrap
{
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

    public function initialize(ServiceManager $serviceManager)
    {
        $directoryList = $this->createDirectoryList();
        $serviceManager->setService('Magento\Framework\App\Filesystem\DirectoryList', $directoryList);
        $serviceManager->setService('Magento\Framework\Filesystem', $this->createFilesystem($directoryList));
    }

    /**
     * Initializes DirectoryList service
     *
     * @return DirectoryList
     */
    public function createDirectoryList()
    {
        $paths = [];
        if (isset($this->config[AppBootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS])) {
            $paths = $this->config[AppBootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS];
        }
        $rootDir = isset($paths[DirectoryList::ROOT][DirectoryList::PATH])
            ? $paths[DirectoryList::ROOT][DirectoryList::PATH]
            : realpath(__DIR__ . '../../../../../..');
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
