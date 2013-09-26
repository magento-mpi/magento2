<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Message resource setup
 */
class Magento_GiftMessage_Model_Resource_Setup extends Magento_Sales_Model_Resource_Setup
{
    /**
     * @var Magento_Catalog_Model_Resource_SetupFactory
     */
    protected $_catalogSetupFactory;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $modulesConfig
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_Catalog_Model_Resource_SetupFactory $catalogSetupFactory
     * @param string $resourceName
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $modulesConfig,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_Catalog_Model_Resource_SetupFactory $catalogSetupFactory,
        $resourceName
    ) {
        $this->_catalogSetupFactory = $catalogSetupFactory;
        parent::__construct($logger, $coreData, $eventManager, $resourcesConfig, $modulesConfig, $moduleList,
            $resource, $modulesReader, $cache, $migrationFactory, $resourceName);
    }

    /**
     * Create Catalog Setup Factory for GiftMessage
     *
     * @param array $data
     * @return Magento_Catalog_Model_Resource_Setup
     */
    public function createGiftMessageSetup(array $data = array())
    {
        return $this->_catalogSetupFactory->create($data);
    }
}
