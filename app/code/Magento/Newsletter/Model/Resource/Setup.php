<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter resource setup
 */
namespace Magento\Newsletter\Model\Resource;

class Setup extends \Magento\Core\Model\Resource\Setup
{
    /**
     * Resource setup model
     *
     * @var \Magento\Core\Model\Resource\Setup\Migration
     */
    protected $_setupMigration;

    /**
     * Construct
     *
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Config\Resource $resourcesConfig
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\Config\Modules\Reader $modulesReader
     * @param string $resourceName
     * @param \Magento\Core\Model\Resource\Setup\MigrationFactory $setupMigrationFactory
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Config\Resource $resourcesConfig,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\Config\Modules\Reader $modulesReader,
        $resourceName,
        \Magento\Core\Model\Resource\Setup\MigrationFactory $setupMigrationFactory
    ) {
        parent::__construct($logger, $eventManager, $resourcesConfig, $config, $moduleList, $resource, $modulesReader,
            $resourceName);

        $this->_setupMigration = $setupMigrationFactory->create(array('resourceName' => 'core_setup'));
    }

    /**
     * Get block factory
     *
     * @return \Magento\Core\Model\Resource\Setup\Migration
     */
    public function getSetupMigration()
    {
        return $this->_setupMigration;
    }
}
