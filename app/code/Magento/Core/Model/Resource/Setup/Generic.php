<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Resource\Setup;

class Generic extends \Magento\Core\Model\Resource\Setup
{
    /**
     * @var \Magento\Core\Model\Resource\Setup\MigrationFactory
     */
    protected $_migrationFactory;

    /**
     * @param \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Config\Resource $resourcesConfig
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\Config\Modules\Reader $modulesReader
     * @param \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory
     * @param $resourceName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory,
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Config\Resource $resourcesConfig,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\Config\Modules\Reader $modulesReader,
        $resourceName
    ) {
        $this->_migrationFactory = $migrationFactory;
        parent::__construct(
            $logger, $eventManager, $resourcesConfig, $config, $moduleList, $resource, $modulesReader, $resourceName
        );
    }

    /**
     * Get migration instance
     *
     * @param array $data
     * @return \Magento\Core\Model\Resource\Setup\Migration
     */
    public function createMigrationSetup(array $data = array())
    {
        return $this->_migrationFactory->create($data);
    }
}
