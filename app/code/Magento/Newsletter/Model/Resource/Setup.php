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
class Magento_Newsletter_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * Resource setup model
     *
     * @var Magento_Core_Model_Resource_Setup_Migration
     */
    protected $_setupMigration;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Core_Model_Resource_Resource $resourceResource
     * @param Magento_Core_Model_Resource_Theme_CollectionFactory $themeResourceFactory
     * @param Magento_Core_Model_Theme_CollectionFactory $themeFactory
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param string $resourceName
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $setupMigrationFactory
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Core_Model_Resource_Resource $resourceResource,
        Magento_Core_Model_Resource_Theme_CollectionFactory $themeResourceFactory,
        Magento_Core_Model_Theme_CollectionFactory $themeFactory,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        $resourceName,
        Magento_Core_Model_Resource_Setup_MigrationFactory $setupMigrationFactory
    ) {
        parent::__construct($logger, $eventManager, $resourcesConfig, $config, $moduleList, $resource,
            $modulesReader, $resourceResource, $themeResourceFactory, $themeFactory, $resourceName);

        $this->_setupMigration = $setupMigrationFactory->create(array('resourceName' => 'core_setup'));
    }

    /**
     * Get block factory
     *
     * @return Magento_Core_Model_Resource_Setup_Migration
     */
    public function getSetupMigration()
    {
        return $this->_setupMigration;
    }
}
