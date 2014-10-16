<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Module;

use Zend\ServiceManager\ServiceLocatorInterface;
use Magento\Setup\Module\Setup\ConfigFactory as DeploymentConfigFactory;
use Magento\Setup\Module\Setup\Config;
use Magento\Setup\Model\LoggerInterface;

class SetupFactory
{
    /**
     * ZF service locator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Deployment config factory
     *
     * @var DeploymentConfigFactory
     */
    private $deploymentConfigFactory;

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param DeploymentConfigFactory $deploymentConfigFactory
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        DeploymentConfigFactory $deploymentConfigFactory
    ) {
        $this->serviceLocator = $serviceLocator;
        $this->deploymentConfigFactory = $deploymentConfigFactory;
    }

    /**
     * Creates Setup
     *
     * @param LoggerInterface $log
     * @return Setup
     */
    public function createSetup(LoggerInterface $log)
    {
        return new Setup(
            $this->serviceLocator->get('Magento\Setup\Module\Setup\ConnectionFactory'),
            $log,
            $this->loadConfig()
        );
    }

    /**
     * Creates SetupModule
     *
     * @param LoggerInterface $log
     * @param string $moduleName
     * @return SetupModule
     */
    public function createSetupModule(LoggerInterface $log, $moduleName)
    {
        return new SetupModule(
            $this->serviceLocator->get('Magento\Setup\Module\Setup\ConnectionFactory'),
            $log,
            $this->loadConfig(),
            $this->serviceLocator->get('Magento\Setup\Module\ModuleList'),
            $this->serviceLocator->get('Magento\Setup\Module\Setup\FileResolver'),
            $moduleName
        );
    }

    /**
     * Load deployment configuration data
     *
     * @return Config
     */
    private function loadConfig()
    {
        $config = $this->deploymentConfigFactory->create();
        $config->loadFromFile();
        return $config;
    }
}
