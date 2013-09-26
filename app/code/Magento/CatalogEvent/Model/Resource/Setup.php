<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event resource setup
 */
class Magento_CatalogEvent_Model_Resource_Setup extends Magento_Sales_Model_Resource_Setup
{
    /**
     * Block model factory
     *
     * @var Magento_Cms_Model_BlockFactory
     */
    protected $_blockFactory;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Core_Model_Resource_Resource $resourceResource
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Resource_Theme_CollectionFactory $themeResourceFactory
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_Cms_Model_BlockFactory $modelBlockFactory
     * @param string $resourceName
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
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_Cms_Model_BlockFactory $modelBlockFactory,
        Magento_Core_Model_CacheInterface $cache,
        $resourceName
    ) {
        $this->_blockFactory = $modelBlockFactory;
        parent::__construct(
            $logger, $eventManager, $resourcesConfig, $config, $moduleList, $resource, $modulesReader,
            $resourceResource, $themeResourceFactory, $migrationFactory, $cache, $resourceName
        );
    }

    /**
     * Get model block factory
     *
     * @return Magento_Cms_Model_BlockFactory
     */
    public function getBlockFactory()
    {
        return $this->_blockFactory;
    }
}
