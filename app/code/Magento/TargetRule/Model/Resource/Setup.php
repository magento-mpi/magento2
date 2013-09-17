<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Setup Resource Model
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_TargetRule_Model_Resource_Setup extends Magento_Catalog_Model_Resource_Setup
{
    /**
     * @var Magento_Enterprise_Model_Resource_Setup_MigrationFactory
     */
    protected $_migrationFactory;

    /**
     * @param Magento_Enterprise_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Core_Model_CacheInterface $cache
     * @param $resourceName
     */
    public function __construct(
        Magento_Enterprise_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Core_Model_CacheInterface $cache,
        $resourceName
    ) {
        $this->_migrationFactory = $migrationFactory;
        parent::__construct(
            $eventManager, $resourcesConfig, $config, $moduleList, $resource, $modulesReader, $cache, $resourceName
        );
    }

    /**
     * Create migration setup
     *
     * @param array $data
     * @return Magento_Enterprise_Model_Resource_Setup_Migration
     */
    public function createMigration(array $data = array())
    {
        return $this->_migrationFactory->create($data);
    }
}
