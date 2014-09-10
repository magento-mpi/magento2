<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Module;

use Magento\Module\Setup\Connection\AdapterInterface;
use Magento\Setup\Model\WebLogger;

class SetupFactory
{
    /**
     * Adapter
     *
     * @var AdapterInterface
     */
    protected $adapter;

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
     * Configurations
     *
     * @var array
     */
    protected $configuration = [];

    /**
     * Default Constructor
     *
     * @param AdapterInterface $connection
     * @param ModuleListInterface $moduleList
     * @param Setup\FileResolver $setupFileResolver
     */
    public function __construct(
        AdapterInterface $connection,
        ModuleListInterface $moduleList,
        Setup\FileResolver $setupFileResolver
    ) {
        $this->adapter = $connection;
        $this->moduleList = $moduleList;
        $this->fileResolver = $setupFileResolver;
    }

    /**
     * Sets Configuration
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->configuration = $config;
        return $this;
    }

    /**
     * Creates Setup
     *
     * @return Setup
     */
    public function createSetup()
    {
        return new Setup(
            $this->adapter,
            $this->fileResolver,
            $this->configuration
        );
    }

    /**
     * Creates SetupModule
     *
     * @param string $moduleName
     * @return SetupModule
     */
    public function createSetupModule($moduleName)
    {
        $setupModule =  new SetupModule(
            $this->adapter,
            $this->moduleList,
            $this->fileResolver,
            $moduleName,
            $this->configuration
        );

        if (isset($this->configuration['db_prefix'])) {
            $setupModule->setTablePrefix($this->configuration['db_prefix']);
        }

        return $setupModule;
    }
}
