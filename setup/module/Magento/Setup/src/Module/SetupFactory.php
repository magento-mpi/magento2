<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Module;

use Magento\Setup\Module\Setup\ConfigFactory as DeploymentConfigFactory;
use Magento\Setup\Module\Setup\ConnectionFactory;
use Magento\Setup\Module\Setup\Config;
use Magento\Setup\Model\LoggerInterface;

class SetupFactory
{
    /**
     * @var DeploymentConfigFactory
     */
    private $deploymentConfigFactory;

    /**
     * Adapter
     *
     * @var ConnectionFactory
     */
    protected $connectionFactory;

    /**
     * List of all Modules
     *
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * File Resolver
     *
     * @var Setup\FileResolver
     */
    protected $fileResolver;

    /**
     * Default Constructor
     *
     * @param DeploymentConfigFactory $deploymentConfigFactory
     * @param ConnectionFactory $connectionFactory
     * @param ModuleListInterface $moduleList
     * @param Setup\FileResolver $setupFileResolver
     */
    public function __construct(
        DeploymentConfigFactory $deploymentConfigFactory,
        ConnectionFactory $connectionFactory,
        ModuleListInterface $moduleList,
        Setup\FileResolver $setupFileResolver
    ) {
        $this->deploymentConfigFactory = $deploymentConfigFactory;
        $this->connectionFactory = $connectionFactory;
        $this->moduleList = $moduleList;
        $this->fileResolver = $setupFileResolver;
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
            $this->connectionFactory,
            $this->fileResolver,
            $log,
            $this->loadConfigData()
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
        $configData = $this->loadConfigData();
        $result = new SetupModule(
            $this->connectionFactory,
            $this->moduleList,
            $this->fileResolver,
            $log,
            $moduleName,
            $configData
        );
        if (isset($configData[Config::KEY_DB_PREFIX])) {
            $result->setTablePrefix($configData[Config::KEY_DB_PREFIX]);
        }
        return $result;
    }

    /**
     * Load deployment configuration data
     *
     * @return array
     */
    private function loadConfigData()
    {
        $config = $this->deploymentConfigFactory->create();
        $config->loadFromFile();
        return $config->getConfigData();
    }
}
