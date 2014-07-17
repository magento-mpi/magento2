<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Module;

use Magento\Module\Setup\Connection\AdapterInterface;

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
        return new Setup(
            $this->adapter,
            $this->moduleList,
            $this->fileResolver,
            $moduleName,
            $this->configuration
        );
    }
}
