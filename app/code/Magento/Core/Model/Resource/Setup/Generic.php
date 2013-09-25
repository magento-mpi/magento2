<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Resource_Setup_Generic extends Magento_Core_Model_Resource_Setup
{
    /**
     * @var Magento_Core_Model_Resource_Setup_MigrationFactory
     */
    protected $_migrationFactory;

    /**
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param $resourceName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
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
     * @return Magento_Core_Model_Resource_Setup_Migration
     */
    public function createMigrationSetup(array $data = array())
    {
        return $this->_migrationFactory->create($data);
    }
}