<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Module;

use Magento\Module\Setup\Connection\AdapterInterface;
use Magento\Setup\Model\Logger;

class SetupFactory
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var Setup\FileResolver
     */
    protected $fileResolver;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param AdapterInterface $connection
     * @param ModuleListInterface $moduleList
     * @param Setup\FileResolver $setupFileResolver
     * @param Logger $logger
     */
    public function __construct(
        AdapterInterface $connection,
        ModuleListInterface $moduleList,
        Setup\FileResolver $setupFileResolver,
        Logger $logger
    ) {
        $this->logger = $logger;
        $this->adapter = $connection;
        $this->moduleList = $moduleList;
        $this->fileResolver = $setupFileResolver;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->configuration = $config;
    }

    /**
     * @param string $moduleName
     * @return Setup
     */
    public function create($moduleName)
    {
        $setup =  new Setup(
            $this->adapter,
            $this->moduleList,
            $this->fileResolver,
            $this->logger,
            $moduleName,
            $this->configuration
        );
        $setup->setTablePrefix($this->configuration['db_prefix']);

        return $setup;
    }
}
