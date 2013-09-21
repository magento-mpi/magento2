<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping resource setup
 */
class Magento_GiftWrapping_Model_Resource_Setup extends Magento_Sales_Model_Resource_Setup
{
    /**
     * @var Magento_Catalog_Model_Product_Type
     */
    protected $_productType;

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
     * @param $resourceName
     * @param Magento_Catalog_Model_Product_Type $productType
     * @param Magento_Catalog_Model_Resource_SetupFactory $catalogSetupFactory
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
        $resourceName,
        Magento_Catalog_Model_Product_Type $productType,
        Magento_Catalog_Model_Resource_SetupFactory $catalogSetupFactory
    ) {
        $this->_productType = $productType;
        $this->_catalogSetupFactory = $catalogSetupFactory;
        parent::__construct(
            $logger,
            $coreData,
            $eventManager,
            $resourcesConfig,
            $modulesConfig,
            $moduleList,
            $resource,
            $modulesReader,
            $cache,
            $resourceName
        );
    }

    /**
     * @return Magento_Catalog_Model_Product_Type
     */
    public function getProductType()
    {
        return $this->_productType;
    }

    /**
     * @return Magento_Catalog_Model_Resource_Setup
     */
    public function getCatalogSetup()
    {
        return $this->_catalogSetupFactory->create(array('resourceName' => 'catalog_setup'));
    }
}
